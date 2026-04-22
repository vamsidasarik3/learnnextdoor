<?php

namespace App\Controllers;

use App\Controllers\AdminBaseController;

use App\Models\UserModel;

class Users extends AdminBaseController
{

    public $title = 'Users Management';
    public $menu = 'users';


    public function index()
    {
        $this->permissionCheck('users_list');
        $users = (new UserModel)->findAll();
        return view('admin/users/list', compact('users'));
    }

	public function add()
	{
        $this->permissionCheck('users_add');
		return view('admin/users/add');
	}

	public function save()
	{
        $this->permissionCheck('users_add');
		postAllowed();

		$id = (new UserModel)->create([
			'role' => post('role'),
			'name' => post('name'),
			'username' => post('username'),
			'email' => post('email'),
			'phone' => post('phone'),
			'address' => post('address'),
			'status' => (int) post('status'),
			'password' => hash( "sha256", post('password') ),
		]);

		if (!empty($_FILES['image']['name'])) {
			$img = $this->request->getFile('image');
			$ext = $img->getExtension();
			$upload = $img->move( FCPATH.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'users', $id.'.'.$ext, true );
			$data['img_type'] = $ext;
			if(!$upload){
				copy(FCPATH.'uploads/users/default.png', 'uploads/users/'.$id.'.png');
				$data['img_type'] = "png";
			}

			(new UserModel)->update($id, ['img_type' => $ext]);
		}else{
			copy(FCPATH.'uploads/users/default.png', 'uploads/users/'.$id.'.png');

		}

		model('App\Models\ActivityLogModel')->add('New User $'.$id.' Created by User:'.logged('name'), logged('id'));
		
		return redirect()->to('users')->with('notifySuccess', 'New User Created Successfully');

	}
	
	public function edit($id)
	{

        $this->permissionCheck('users_edit');

		$user = (new UserModel)->getById($id);
		return view('admin/users/edit', compact('user'));

	}
	
	public function update($id)
	{

        $this->permissionCheck('users_edit');
		postAllowed();

		$data = [
			'role' => post('role'),
			'name' => post('name'),
			'username' => post('username'),
			'email' => post('email'),
			'phone' => post('phone'),
			'address' => post('address'),
		];

		$password = post('password');

		if(logged('id')!=$id)
			$data['status'] = post('status')==1;

		if(!empty($password))
			$data['password'] = hash( "sha256", $password );


		$img = $this->request->getFile('image');
		
		if (!empty($_FILES['image']['name'])) {

			$ext = $img->getExtension();
			$upload = $img->move( FCPATH.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'users', $id.'.'.$ext, true );
			if(!$upload){
				return redirect()->back()->with('notifyError', 'Server Error Occured while Uploading Image !');
			}
			$data['img_type'] = $ext;

			$id = (new UserModel)->update($id, $data);
		}else{
			$id = (new UserModel)->update($id, $data);
		}

		model('App\Models\ActivityLogModel')->add("User #$id Updated by User:".logged('name'));
		
		return redirect()->to('users')->with('notifySuccess', 'New User Created Successfully');

	}

	public function view($id)
	{

        $this->permissionCheck('users_view');

		$user = (new UserModel)->getById($id);
		$user->role = model('App\Models\RoleModel')->getByWhere([
			'id'=> $user->role
		])[0];
		$user->activity = model('App\Models\ActivityLogModel')->getByWhere([
			'user'=> $id
		], [ 'order' => ['id', 'desc'] ]);

		return view('admin/users/view', compact('user'));

	}


	public function check()
	{
		$email = !empty(get('email')) ? get('email') : false;
		$username = !empty(get('username')) ? get('username') : false;
		$notId = !empty(get('notId')) ? get('notId') : 0;

		if($email)
			$exists = count((new UserModel)->getByWhere([
					'email' => $email,
					'id !=' => $notId,
				])) > 0 ? true : false;

		if($username)
			$exists = count((new UserModel)->getByWhere([
					'username' => $username,
					'id !=' => $notId,
				])) > 0 ? true : false;

		echo $exists ? 'false' : 'true';
	}

    /**
     * API: Toggle User Status (Suspend/Reinstate)
     * POST /users/change_status/(:num)
     */
    public function change_status($id)
    {
        $this->permissionCheck('users_edit');
        if($id == 1 || $id == logged('id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You cannot change your own status or the super-admin status.']);
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);

        $newStatus = $this->request->getPost('status') == 'true' || $this->request->getPost('status') == 1 ? 1 : 0;
        $remarks   = $this->request->getPost('remarks');

        if ($newStatus == 0 && empty(trim($remarks))) {
            return $this->response->setJSON(['success' => false, 'message' => 'A reason is required to suspend a user.']);
        }

        $userModel->update($id, [
            'status'         => $newStatus,
            'status_remarks' => $remarks
        ]);

        // LOG IN AUDIT LOG
        $action = ($newStatus == 1) ? 'REINSTATED' : 'SUSPENDED';
        $logMsg = "User #{$id} ({$user->name}) was {$action} by Admin: " . logged('name') . ". Reason: " . ($remarks ?: 'N/A');
        model('App\Models\ActivityLogModel')->add($logMsg, logged('id'));

        return $this->response->setJSON(['success' => true, 'message' => "User status updated to {$action}."]);
    }

	public function delete($id)
	{

        $this->permissionCheck('users_delete');

		if($id!==1 && $id!=logged('id')){ }else{
			return redirect()->to('users');
		}

		(new UserModel)->delete($id);

		model('App\Models\ActivityLogModel')->add("User #$id Deleted by User:".logged('name'));
		
		return redirect()->to('users')->with('notifySuccess', 'User has been Deleted Successfully');

	}

}
