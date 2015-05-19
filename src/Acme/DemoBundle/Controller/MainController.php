<?php

namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Entity\User;
use Acme\DemoBundle\Form\Model\Registration;
use Acme\DemoBundle\Form\Type\RegistrationType;
use Acme\DemoBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 *@Route("/")
 */
class MainController extends Controller
{
    /**
     * @Route("register", name = "register_method")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction()
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('create_method'),
        ));

        return $this->render(
            'userpage/register.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("create", name = "create_method")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $registration = $form->getData();
            $currentUser = $registration->getUser();
            $currentEmail = $currentUser->getEmail();
            $currentToken = md5($currentEmail);
            $currentUser->setToken(md5($currentEmail));


            // sending email
            $message = \Swift_Message::newInstance()
                ->setSubject('registration')
                ->setFrom('ea.netpos.proj1@gmail.com')
                ->setTo('elterandrasb@gmail.com')
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'Emails/registration.html.twig',
                        array('link' => 'http://userpage/app_dev.php/registered/'.$currentToken)
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $em->persist($currentUser);
            $em->flush();
        }

        return $this->render(
            'userpage/register.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("registered/{token}", name="registered_method")
     */
    public function registeredAction($token)
    {
        $current_dt = new \DateTime('now');

        $user = $this->getDoctrine()
            ->getRepository('AcmeDemoBundle:User')
            ->findOneBy(array('token'=>$token));
        $user->setIsActive(true);
        $user->setCreated($current_dt);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $id = $user->getId();

        return $this->redirectToRoute('login_route');
    }


    /**
     * @Route("admin", name="admin")
     */
    public function adminAction()
    {
        // storing login date and IP
        $current_datetime = new \DateTime('now');
        $current_IP = $_SERVER['REMOTE_ADDR'];

        $token_obj = $this->get('security.context')->getToken();
        $token = $token_obj->getUser()->getToken();

        $user = $this->getDoctrine()
            ->getRepository('AcmeDemoBundle:User')
            ->findOneBy(array('token'=>$token));
        $user->setLastLogin($current_datetime);
        $user->setClientIP($current_IP);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        //

        $user = $token_obj->getUser();
        $full_name = $user->getFullName();
        $email = $user->getEmail();
        $phone = $user->getPhone();

        return $this->render('security/adminpage.html.twig', array(
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone
        ));
    }


    /**
     * @Route("profile", name = "user_profile")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction()
    {
        // storing login date and IP
        $current_datetime = new \DateTime('now');
        $current_IP = $_SERVER['REMOTE_ADDR'];

        $token_obj = $this->get('security.context')->getToken();
        $token = $token_obj->getUser()->getToken();

        $user = $this->getDoctrine()
            ->getRepository('AcmeDemoBundle:User')
            ->findOneBy(array('token' => $token));
        $user->setLastLogin($current_datetime);
        $user->setClientIP($current_IP);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        //

        $user = $token_obj->getUser();
        $full_name = $user->getFullName();
        $email = $user->getEmail();
        $phone = $user->getPhone();

        return $this->render('security/adminpage.html.twig', array(
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone
        ));
    }

}