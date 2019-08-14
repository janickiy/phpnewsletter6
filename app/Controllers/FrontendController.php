<?php

namespace App\Controllers;

use App\Models\{ReadySent, RedirectLog, Subscribers};

class FrontendController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
    public function pic($request, $response, $parameter)
    {

        if (isset($parameter['subscriber']) && isset($parameter['template'])){

            ReadySent::where('templateId',$parameter['template'])->where('subscriberId',$parameter['subscriber'])->update(['readmail' => 1]);
        }

        $img = ImageCreateTrueColor(1,1);
        ob_start();
        imagegif($img);
        $image = ob_get_clean();

        $response->write($image);

        return $response->withHeader('Content-Type', 'image/gif');

    }

    /**
     * @param $request
     * @param $response
     * @param $parameter
     * @return mixed
     */
    public function redirectLog($request, $response, $parameter)
    {
        $url = isset($parameter['ref']) ? base64_decode($parameter['ref']) : '';
        $subscriberId = isset($parameter['subscriber']) ? $parameter['subscriber'] : '';

        if ($url && $subscriberId) {
            $subscriber = Subscribers::find($subscriberId);

            if (!$subscriber) return $this->view->render($response, 'errors/404.twig');

            $data['url'] = $url;
            $data['time'] = date("Y-m-d H:i:s");
            $data['email'] = isset($subscriber->email) ? $subscriber->email : '';

            RedirectLog::create($data);

            return $response->withRedirect($url);

        } else {
            return $this->view->render($response, 'errors/500.twig');
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $parameter
     * @return mixed
     */
    public function unsubscribe($request, $response, $parameter)
    {
        $token = isset($parameter['token']) ? $parameter['token'] : '';
        $subscriberId = isset($parameter['subscriber']) ? $parameter['subscriber'] : '';

        if ($token && $subscriberId) {
            $result = Subscribers::where('id',$subscriberId);

            $subscriber = $result->first();

            if (!$subscriber) return $this->view->render($response, 'errors/404.twig');

            if ($subscriber->token == $token) {
                $result->update(['active' => 0]);

                return $this->view->render($response, 'frontend/unsubscribe.twig');

            } else {
                return $this->view->render($response, 'errors/500.twig');
            }
        } else {
            return $this->view->render($response, 'errors/500.twig');
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $parameter
     * @return mixed
     */
    public function subscribe($request, $response, $parameter)
    {
        $token = isset($parameter['token']) ? $parameter['token'] : '';
        $subscriberId = isset($parameter['subscriber']) ? $parameter['subscriber'] : '';

        if ($token && $subscriberId) {
            $result = Subscribers::where('id',$subscriberId);

            $subscriber = $result->first();

            if (!$subscriber) return $this->view->render($response, 'errors/404.twig');


            if ($subscriber->token == $token) {
                $result->update(['active' => 1]);

                return $this->view->render($response, 'frontend/subscribe.twig');

            } else {
                return $this->view->render($response, 'errors/500.twig');
            }
        } else {
            return $this->view->render($response, 'errors/500.twig');
        }
    }




}