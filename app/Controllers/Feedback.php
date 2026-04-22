<?php
namespace App\Controllers;

use App\Models\FeedbackModel;

class Feedback extends AdminBaseController
{
    public function index()
    {
        $this->permissionCheck('listings_view');
        
        $model = new FeedbackModel();
        $status = $this->request->getGet('status');
        
        $feedbacks = $model->getAll($status);

        return view('admin/feedback/list', [
            'feedbacks' => $feedbacks,
            'status'    => $status,
            'title'     => 'Support Tickets',
            '_page'     => (object)[
                'title' => 'Support Tickets',
                'menu'  => 'feedback'
            ]
        ]);
    }

    public function view($id)
    {
        $this->permissionCheck('listings_view');
        $model = new FeedbackModel();
        $feedback = $model->find($id);

        if (!$feedback) {
            return redirect()->to('admin/feedback')->with('error', 'Ticket not found.');
        }

        if ($feedback->status === 'new') {
            $model->markRead($id);
        }

        return view('admin/feedback/view', [
            'feedback' => $feedback,
            'title'    => 'View Ticket'
        ]);
    }
}
