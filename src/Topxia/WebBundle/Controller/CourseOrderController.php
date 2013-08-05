<?php
namespace Topxia\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Topxia\Common\ArrayToolkit;
use Topxia\Component\Payment\Payment;

class CourseOrderController extends BaseController
{

    public function buyAction(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (empty($user['id'])) {
            throw $this->createAccessDeniedException();
        }

        $course = $this->getCourseService()->getCourse($id);

        $data = array('courseId' => $course['id'], 'payment' => 'alipay');
        $form = $this->createNamedFormBuilder('course_order', $data)
            ->add('courseId', 'hidden')
            ->add('payment', 'hidden')
            ->getForm();

        return $this->render('TopxiaWebBundle:CourseOrder:buy-modal.html.twig', array(
            'course' => $course,
            'form' => $form->createView()
        ));
    }

    public function payAction(Request $request)
    {
        $order = $this->getOrderService()->createOrder($request->request->all());

        $paymentRequest = $this->createPaymentRequest($order);

        return $this->render('TopxiaWebBundle:CourseOrder:pay.html.twig', array(
            'form' => $paymentRequest->form(),
            'order' => $order,
        ));
    }

    public function payReturnAction(Request $request, $name)
    {
        $response = $this->createPaymentResponse($name, $request->query->all());

        $payData = $response->getPayData();
        $order = $this->getOrderService()->payOrder($payData);

        return $this->redirect($this->generateUrl('course_show', array('id' => $order['courseId'])));
    }

    public function payNotifyAction(Request $request)
    {

    }

    private function createPaymentRequest($order)
    {

        $options = $this->getPaymentOptions($order['payment']);
        $request = Payment::createRequest($order['payment'], $options);

        return $request->setParams(array(
            'orderSn' => $order['sn'],
            'title' => $order['title'],
            'summary' => '',
            'amount' => $order['price'],
            'returnUrl' => $this->generateUrl('course_order_pay_return', array('name' => $order['payment']), true),
            'notifyUrl' => $this->generateUrl('course_order_pay_notify', array('name' => $order['payment']), true),
            'showUrl' => $this->generateUrl('course_show', array('id' => $order['courseId']), true),
        ));
    }

    private function createPaymentResponse($name, $params)
    {
        $options = $this->getPaymentOptions($name);
        $response = Payment::createResponse($name, $options);

        return $response->setParams($params);
    }

    private function getPaymentOptions($payment)
    {
        $settings = $this->setting('payment');

        if (empty($settings)) {
            throw new \RuntimeException('支付参数尚未配置，请先配置。');
        }

        if (empty($settings['enabled'])) {
            throw new \RuntimeException("支付模块未开启，请先开启。");
        }

        if (empty($settings[$payment. '_enabled'])) {
            throw new \RuntimeException("支付模块({$payment})未开启，请先开启。");
        }

        if (empty($settings["{$payment}_key"]) or empty($settings["{$payment}_secret"])) {
            throw new \RuntimeException("支付模块({$payment})参数未设置，请先设置。");
        }

        $options = array(
            'key' => $settings["{$payment}_key"],
            'secret' => $settings["{$payment}_secret"],
        );

        return $options;
    }

    private function getOrderService()
    {
        return $this->getServiceKernel()->createService('Course.OrderService');
    }


    private function getCourseService()
    {
        return $this->getServiceKernel()->createService('Course.CourseService');
    }

}