<?php

namespace App\Controller;

use App\Entity\ReceiptRegistration;
use App\Repository\ReceiptRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

//testing github desktop

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(): Response
    {
        $currentDate = new \DateTime();
        $startDate = new \DateTime($this->getParameter("campaign_start_date"));
        $endDate = new \DateTime($this->getParameter("campaign_end_date"));
        if (!($currentDate >= $startDate && $currentDate <= $endDate)) {
            return $this->redirectToRoute('teaser');
        }
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/how-to-win", name="how-to-win")
     */
    public function howToWin(): Response
    {
        return $this->render('main/how_to_win.html.twig');
    }

    /**
     * @Route("/winners", name="winners")
     */
    public function winners(): Response
    {
        return $this->render('main/winners.html.twig');
    }

    /**
     * @Route("/rules", name="rules")
     */
    public function rules(): Response
    {
        return $this->render('main/rules.html.twig');
    }

    /**
     * @Route("/cookies-policy", name="cookies-policy")
     */
    public function cookies(): Response
    {
        return $this->render('main/cookies_policy.html.twig');
    }

    /**
     * @Route("/terms-and-conditions", name="terms-and-conditions")
     */
    public function termsAndConditions(): Response
    {
        return $this->render('main/terms_and_conditions.html.twig');
    }

    /**
     * @Route("/teaser", name="teaser")
     */
    public function teaser(): Response
    {
        $currentDate = new \DateTime();
        $startDate = new \DateTime($this->getParameter("campaign_start_date"));
        $endDate = new \DateTime($this->getParameter("campaign_end_date"));
        if ($currentDate >= $startDate && $currentDate <= $endDate) {
            return $this->redirectToRoute('main');
        }
        return $this->render('main/teaser.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(): Response
    {
        return $this->render('main/register.html.twig');
    }

    /**
     * @Route("/register-campaign", name="register-campaign")
     */
    public function registerCampaign(Request $request, EntityManagerInterface $em, ReceiptRegistrationRepository $receiptRegistrationRepository): Response
    {

        $receiptRegistration = new ReceiptRegistration();
        $currentDate = new \DateTime();
        $campaignStartDate = new \DateTime($this->getParameter("campaign_start_date"));
        $currentSubmitTime = new \DateTimeImmutable();
        $endSubmitDate = new \DateTime($this->getParameter("campaign_end_date"));

        $idNet = microtime(true) * 1000;

        $minute = (int)date('i');
        $prizes = array('Voucher Eventim', 'Camera foto instant Fujifilm Instax Mini 11');
        $prizeMessages = array(
            'Felicitări! Ai câștigat un voucher Eventim în valoare de 300 LEI. Păstrează bonul fiscal, te vom contacta în curând. Înscrie și alte bonuri fiscale, pentru o șansă la premiul cel mare. Ai dreptul la cel mult zece înscrieri în fiecare săptămână de campanie.',
            'Felicitări! Ai câștigat o Camera foto instant. Păstrează bonul fiscal, te vom contacta în curând. Înscrie și alte bonuri fiscale, pentru o șansă la premiul cel mare. Ai dreptul la cel mult zece înscrieri în fiecare săptămână de campanie.'
        );

        $jsonResponse = [
            'success' => true,
            'errors' => [],
            'status' => 1,
            'prize' => 0,
            'messageStatus' => '',
            'message' => ''
        ];

        if ($currentDate < $campaignStartDate) {
            $jsonResponse['messageStatus'] = 'precampanie';
            $jsonResponse['message'] = 'Bucură-te de SNACKFEST!" nu a început încă. Mai ai un picuț de răbdare. Ca să te înscrii în campanie, trebuie să cumperi produse participante în valoare de minimum 15 lei, pe același bon fiscal, în perioada 3.07.2023 – 13.08.2023, și să înscrii bonul pe www.gustulcalatoriei.ro.';
            return new JsonResponse($jsonResponse, 200);
        }

        if ($currentDate >= $endSubmitDate) {
            $jsonResponse['messageStatus'] = 'postcampanie';
            $jsonResponse['message'] = 'Uf, ai întârziat puțin. Campania " Bucură-te de SNACKFEST!" s-a încheiat pe data de 13.08.2023. Îți mulțumim pentru participare și te așteaptăm la următoarele campanii promoționale.';
            return new JsonResponse($jsonResponse, 200);
        }

        $telefon = $request->get('phone');
        $nrBon = $request->get('receiptNumber');
        $dataBon = $request->get('date');
        $acordTermeni = $request->get('terms');
        $acordVarsta = $request->get('age');
        $acordRegulament = $request->get('rule');
        $magazin = $request->get('store');

        /*$recaptcha = $request->get('g-recaptcha-response');
        $recaptchaResponse = $this->captchaVerify($recaptcha);*/

        if (empty($telefon) || empty($nrBon) || empty($dataBon) || empty($acordTermeni) || empty($acordVarsta) || empty($acordRegulament) || empty($magazin)) {
            $jsonResponse['messageStatus'] = 'incorect';
            $jsonResponse['message'] = 'Lipsesc unul sau mai multi parametri.';
            return new JsonResponse($jsonResponse, 200);
        }

        /*if ($recaptchaResponse !== true) {
            $jsonResponse['messageStatus'] = 'recaptcha';
            $jsonResponse['message'] = 'Nu uita să bifezi recaptcha!';
            return new JsonResponse($jsonResponse, 200);
        }*/


        $receiptDateValidator = Validation::createValidator();
        $constraintReceiptDate = new Assert\Regex([
            'pattern' => '/^\d{2}\/\d{2}\/\d{4}$/',
            'message' => 'Data bonului nu este în formatul așteptat (zz/ll/aaaa).',
        ]);
        $errorsReceiptDate = $receiptDateValidator->validate($dataBon, $constraintReceiptDate);

        if (count($errorsReceiptDate) > 0) {
            $jsonResponse['messageStatus'] = 'INVALIDPARAMS';
            $jsonResponse['message'] = 'Data bonului nu este în formatul așteptat (zz/ll/aaaa).';
            return new JsonResponse($jsonResponse, 200);
        }

        $phoneNumberValidator = Validation::createValidator();
        $constraintPhoneNumber = new Assert\Regex([
            'pattern' => '/^07\d{8}$/',
            'message' => 'Numărul de telefon nu este un numar valid din Romania.'
        ]);
        $errorsPhoneNumber = $phoneNumberValidator->validate($telefon, $constraintPhoneNumber);

        if (count($errorsPhoneNumber) > 0) {
            $jsonResponse['messageStatus'] = 'INVALIDPARAMS';
            $jsonResponse['message'] = 'Numărul de telefon nu este un numar valid din Romania.';
            return new JsonResponse($jsonResponse, 200);
        }

        $receiptCodeValidator = Validation::createValidator();
        $constraintReceiptCode = new Assert\Regex([
            'pattern' => '/^\d+$/',
            'message' => 'Codul bonului trebuie să conțină doar cifre.',
        ]);
        $errorsReceiptCode = $receiptCodeValidator->validate($nrBon, $constraintReceiptCode);

        if (count($errorsReceiptCode) > 0) {
            $jsonResponse['messageStatus'] = 'INVALIDPARAMS';
            $jsonResponse['message'] = 'Codul bonului trebuie sa contina doar cifre.';
            return new JsonResponse($jsonResponse, 200);
        }


        $receiptRegistration->setSubmittedAt($currentSubmitTime);
        $receiptWeekCount = $receiptRegistrationRepository->weekReceiptsCounter($telefon, $currentSubmitTime);

        if ($receiptWeekCount >= 10) {
            $lastWeekStart = (new \Datetime($this->getParameter("campaign_end_date")))->format('-7 days');
            $endDate = new \DateTime($this->getParameter("campaign_end_date"));

            if ($currentSubmitTime >= $lastWeekStart && $currentSubmitTime <= $endDate) {
                $jsonResponse['messageStatus'] = 'blocat_corecte';
                $jsonResponse['message'] = 'Bucură-te de SNACKFEST!” se încheie săptămâna aceasta și se pare că ai atins limita de înscrieri. Mulțumim pentru participare. Verifică rezultatul tragerii la sorți și vezi dacă te afli printre câștigători!';
                return new JsonResponse($jsonResponse, 200);
            }

            $jsonResponse['messageStatus'] = 'blocat_corecte';
            $jsonResponse['message'] = 'Ne bucurăm că-ți plac atât de mult produsele noastre, dar se pare că ai atins limita de zece înscrieri într-o săptămână de campanie. Mulțumim pentru participare și te așteptăm pentru noi înscrieri săptămâna viitoare.';
            return new JsonResponse($jsonResponse, 200);
        }

        /*$existingReceipt = $receiptRegistrationRepository->findOneBy(['receiptCode' => $nrBon]);
        $existingReceiptDate = $receiptRegistrationRepository->findOneBy(['receiptDate' => $dataBon]);
        $existingPhoneNumber = $receiptRegistrationRepository->findOneBy(['phoneNumber' => $telefon]);
        $existingStore = $receiptRegistrationRepository->findOneBy(['store' => $magazin]);*/

        $values = [
            'receiptCode' => $nrBon,
            'receiptDate' => $dataBon,
            'phoneNumber' => $telefon,
            'store' => $magazin
        ];

        $existingValues = $receiptRegistrationRepository->findBy($values);


        if ($existingValues) {
            $jsonResponse['messageStatus'] = 'dubla';
            $jsonResponse['message'] = 'Acest număr de bon fiscal a mai fost înscris în campanie. Pentru a putea participa, te rugăm să introduci alt bon fiscal.';
            return new JsonResponse($jsonResponse, 200);
        }

        $receiptRegistration->setPhoneNumber($telefon);
        $receiptRegistration->setIdNet($idNet);
        $receiptRegistration->setReceiptCode($nrBon);
        $receiptRegistration->setReceiptDate($dataBon);
        $receiptRegistration->setAcordTermeni($acordTermeni);
        $receiptRegistration->setAcordVarsta($acordVarsta);
        $receiptRegistration->setAcordRegulament($acordRegulament);
        $receiptRegistration->setStore($magazin);

        if ($minute < 59) {
            $randomPrizeIndex = array_rand($prizes);

            $em->persist($receiptRegistration);
            $em->flush();
            //dd($receiptRegistration);

            $jsonResponse['prize'] = $randomPrizeIndex + 1;
            $jsonResponse['messageStatus'] = 'prize';
            $jsonResponse['message'] = $prizeMessages[$randomPrizeIndex];
            return new JsonResponse($jsonResponse, 200);
        }

        $em->persist($receiptRegistration);
        $em->flush();

        $jsonResponse['messageStatus'] = 'corect';
        $jsonResponse['message'] = 'Felicitări! Te-ai înscris cu succes în campania " Bucură-te de SNACKFEST!". Îți reamintim că, pentru mai multe șanse de câștig, poți înregistra până la 10 bonuri fiscale pe săptămână de campanie . Și nu uita să păstrezi pentru validare bonul fiscal! Succes!';
        return new JsonResponse($jsonResponse, 200);
    }

    /**
     * Validate reCaptcha
     *
     * @param $recaptcha
     * @return mixed
     */
    private function captchaVerify($recaptcha)
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                "secret" => $this->getParameter('google_recaptcha_secret_key'),
                "response" => $recaptcha
            )
        );

        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        return $data->success;
    }
}
