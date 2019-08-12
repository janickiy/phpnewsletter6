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
    public function redirectLog($request, $response,$parameter)
    {
        $url = isset($parameter['ref']) ? base64_decode($parameter['ref']) : '';
        $subscriberId = isset($parameter['subscriber']) ? $parameter['subscriber'] : '';

        if ($url && $subscriberId) {
            $subscriber = Subscribers::find($subscriberId);

            $data['url'] = $url;
            $data['time'] = date("Y-m-d H:i:s");
            $data['email'] = isset($subscriber->email) ? $subscriber->email : '';

            RedirectLog::create($data);

            return $response->withRedirect($url);

        } else {
            return $this->view->render($response, 'errors/500.twig');
        }
    }

}