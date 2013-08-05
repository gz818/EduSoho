<?php
namespace Topxia\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Topxia\WebBundle\Form\RegisterType;

class RegisterController extends BaseController
{

    public function indexAction(Request $request)
    {
        $form = $this->createForm(new RegisterType());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $registration = $form->getData();

                $user = $this->getUserService()->register($registration);
                $this->authenticateUser($user);
                $this->get('session')->set('registed_email', $user['email']);
                return $this->redirect($this->generateUrl('register_submited', array(
                    'id' => $user['id'], 'hash' => $this->makeHash($user))
                ));
            }
        }

        return $this->render("TopxiaWebBundle:Register:index.html.twig", array(
            'form' => $form->createView()
        ));
    }

    public function emailSendAction(Request $request, $id, $hash)
    {
        $user = $this->checkHash($id, $hash);
        if (empty($user)) {
            return $this->createJsonResponse(false);
        }

        $token = $this->getUserService()->makeToken('email-verify', $user['id'], strtotime('+1 day'));

        $this->sendEmail(
            $user['email'],
            "请激活你的帐号，完成注册",
            $this->renderView('TopxiaWebBundle:Register:email-verify.txt.twig', array(
                'user' => $user,
                'token' => $token,
            ))
        );
        return $this->createJsonResponse(true);
    }

    public function submitedAction(Request $request, $id, $hash)
    {
        $user = $this->checkHash($id, $hash);
        if (empty($user)) {
            throw $this->createNotFoundException();
        }

        return $this->render("TopxiaWebBundle:Register:submited.html.twig", array(
            'user' => $user,
            'hash' => $hash,
            'emailLoginUrl' => $this->getEmailLoginUrl($user['email']),
        ));
    }

    public function emailVerifyAction(Request $request, $token)
    {

        $token = $this->getUserService()->getToken('email-verify', $token);
        if (empty($token)) {
            $currentUser = $this->getCurrentUser();
            if (empty($currentUser)) {
                return $this->render('TopxiaWebBundle:Register:email-verify-error.html.twig');
            } else {
                return $this->redirect($this->generateUrl('settings'));
            }
        }

        $user = $this->getUserService()->getUser($token['userId']);
        if (empty($user)) {
            return $this->createNotFoundException();
        }

        $this->getUserService()->setEmailVerified($user['id']);

        $this->getUserService()->deleteToken('email-verify', $token['token']);

        return $this->render('TopxiaWebBundle:Register:email-verify-success.html.twig');
    }

    private function makeHash($user)
    {
        $string = $user['id'] . $user['email'] . $this->container->getParameter('secret');
        return md5($string);
    }

    private function checkHash($userId, $hash)
    {
        $user = $this->getUserService()->getUser($userId);
        if (empty($user)) {
            return false;
        }

        if ($this->makeHash($user) !== $hash) {
            return false;
        }

        return $user;
    }

    public function emailCheckAction(Request $request)
    {
        $email = $request->query->get('value');
        $result = $this->getUserService()->isEmailAvaliable($email);
        if ($result) {
            $response = array('success' => true, 'message' => '该Email地址可以使用');
        } else {
            $response = array('success' => false, 'message' => '该Email地址已经被占用了');
        }
        return $this->createJsonResponse($response);
    }

    public function nicknameCheckAction(Request $request)
    {
        $nickname = $request->query->get('value');
        $result = $this->getUserService()->isNicknameAvaliable($nickname);
        if ($result) {
            $response = array('success' => true, 'message' => '该昵称可以使用');
        } else {
            $response = array('success' => false, 'message' => '该昵称已经被占用了');
        }
        return $this->createJsonResponse($response);
    }

    public function getEmailLoginUrl ($email)
    {
        $host = substr($email, strpos($email, '@') + 1);
        
        if ($host == 'hotmail.com') {
            return 'http://www.' . $host;
        }
        
        if ($host == 'gmail.com') {
            return 'http://mail.google.com';
        }
        
        return 'http://mail.' . $host;
    }

}