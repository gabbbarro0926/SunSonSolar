<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Models\FaqModel;
use App\Models\PostModel;
use App\Models\ProductModel;
use App\Models\QuoteRequestModel;
use App\Models\ServiceModel;
use App\Models\TestimonialModel;

/** Everything the public homepage needs — no login required. */
class PublicController extends BaseApiController
{
    /** GET /api/public/home */
    public function home()
    {
        return $this->ok([
            'company' => [
                'name'    => 'Sun Son Solar',
                'tagline' => 'Power Your Future with Solar',
                'about'   => 'Sun Son Solar supplies solar panels, inverters, batteries, racking and mounting, and wires, and provides consultation, designing, permitting, installation, maintenance, repair, and monitoring services. Based in Pasig City.',
                'benefits' => [
                    ['title' => 'Cost savings',        'text' => 'Lower your monthly electricity bill.'],
                    ['title' => 'Sustainability',      'text' => 'Reduce your carbon footprint with clean energy.'],
                    ['title' => 'Energy independence', 'text' => 'Rely less on the grid and rising power rates.'],
                ],
                'main_services' => ['Consultation', 'Installation', 'Maintenance'],
            ],
            'services'     => (new ServiceModel())->where('is_active', 1)->orderBy('id')->findAll(),
            'testimonials' => (new TestimonialModel())->where('is_published', 1)->orderBy('id', 'DESC')->findAll(6),
            'faqs'         => (new FaqModel())->where('is_published', 1)->orderBy('sort_order')->findAll(),
            'latest_posts' => (new PostModel())->where('is_published', 1)->orderBy('id', 'DESC')->findAll(3),
        ]);
    }

    /** GET /api/public/services */
    public function services()
    {
        return $this->ok((new ServiceModel())->where('is_active', 1)->orderBy('id')->findAll());
    }

    /** GET /api/public/products?category=panels */
    public function products()
    {
        $m = (new ProductModel())->where('is_active', 1);
        if ($cat = $this->request->getGet('category')) {
            $m->where('category', $cat);
        }

        return $this->ok($m->orderBy('category')->orderBy('name')->findAll());
    }

    /** GET /api/public/posts */
    public function posts()
    {
        return $this->ok((new PostModel())->where('is_published', 1)->orderBy('id', 'DESC')->findAll());
    }

    /** GET /api/public/posts/(:num) */
    public function post(int $id)
    {
        $p = (new PostModel())->where('is_published', 1)->find($id);

        return $p ? $this->ok($p) : $this->notFound('Post');
    }

    /** POST /api/public/quotes — "request a free quote" */
    public function quote()
    {
        $data = $this->input();
        $rules = [
            'name'       => 'required|max_length[150]',
            'email'      => 'required|valid_email',
            'phone'      => 'permit_empty|max_length[30]',
            'address'    => 'permit_empty',
            'service_id' => 'permit_empty|is_natural_no_zero|is_not_unique[services.id]',
            'message'    => 'permit_empty|max_length[2000]',
        ];
        if ($res = $this->check($data, $rules)) {
            return $res;
        }

        $m  = new QuoteRequestModel();
        $id = $m->insert([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'address'    => $data['address'] ?? null,
            'service_id' => $data['service_id'] ?? null,
            'message'    => $data['message'] ?? null,
            'status'     => 'new',
        ]);

        return $this->ok($m->find($id), 'Thanks! We will contact you about your free quote.', 201);
    }
}
