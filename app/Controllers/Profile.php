<?php
namespace App\Controllers;

use App\Controllers\AdminBaseController;

use App\Models\UserModel;
use App\Models\RoleModel;

class Profile extends AdminBaseController
{

    public $title = 'Profile Management';
    public $menu = false;


	public function index($tab = 'profile')
	{
		$user = (new UserModel)->getById(logged('id'));
		$user->role = (new RoleModel)->getById( logged('role') );
		$activeTab = $tab;

		if (logged('role') == 1) {
			return view('admin/account/profile', compact('user', 'activeTab'));
		} 
		
		$docModel = new \App\Models\UserDocumentModel();

		// Case 1: Provider Role (2)
		if (logged('role') == 2) {
			return view('frontend/provider/profile', [
				'user'              => $user,
				'documents'         => $docModel->getByUser(logged('id')),
				'activeTab'         => $activeTab,
				'page_title'        => 'Provider Account | Class Next Door',
				'show_location_bar' => false,
			]);
		} 
		
		// Case 2: Parent / General User Role (3)
		return view('frontend/user_profile', [
			'user'              => $user,
			'activeTab'         => $activeTab,
			'page_title'        => 'My Account | Class Next Door',
			'show_location_bar' => true,
		]);
	}

	public function updateProfile()
	{

		$id = logged('id');
		
		postAllowed();

		$data = [
			'name' => post('name'),
		];

		// Only update username/address if they are sent (prevent null-overwrites)
		if (post('username') !== null) {
			$data['username'] = post('username');
		}
		if (post('address') !== null) {
			$data['address'] = post('address');
		}
		if (post('upi_id') !== null) {
			$data['upi_id'] = post('upi_id');
		}

		$id = (new UserModel)->update($id, $data);

		model('App\Models\ActivityLogModel')->add("User #$id updated the profile");
		
		return redirect()->to('profile/index/edit')->with('notifySuccess', 'Profile has been Updated Successfully');

	}

	public function updatePassword()
	{

		$id = logged('id');
		
		postAllowed();

		if ( post('password') !== post('password_confirm') ) {
			return redirect()->to('profile/index/change_password')->with('notifyError', 'Password does not matches with Confirm Password !');
		}
		
		if ( strlen(post('password')) < 6 ) {
			return redirect()->to('profile/index/change_password')->with('notifyError', 'Password must have atleast 6 Characters');
		}

		$userModel = new UserModel();
		$currentHash = $userModel->getRowById($id, 'password');
		$oldPassword = post('old_password');
		
		$valid = password_verify($oldPassword, $currentHash) 
               || ($currentHash === hash('sha256', $oldPassword));

		if (!$valid) {
			return redirect()->to('profile/index/change_password')->with('notifyError', 'Invalid Old Password !');
		}


		$password = post('password');
		$data['password'] = password_hash($password, PASSWORD_BCRYPT);

		$id = $userModel->update($id, $data);

		model('App\Models\ActivityLogModel')->add("User #$id changed the password !");

		return redirect()->to('login')->with('notifySuccess', 'Password Changed, You need to Login Again !');

	}

	public function updateProfilePic()
	{
		if (! $this->validate([
            'image' => [
                'label' => 'Profile Image',
                'rules' => 'uploaded[image]'
                    . '|is_image[image]'
                    . '|mime_in[image,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
            ],
        ])) {
            // $data = ['errors' => $this->validator->getErrors()];
			return redirect()->to('profile/index/change_pic')->with('notifyError', $this->validator->getErrors()['image']);
        }

		$id = logged('id');
		
		$img = $this->request->getFile('image');
		
		if (!empty($_FILES['image']['name'])) {

			$ext = $img->getExtension();
			$upload = $img->move( FCPATH.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'users', $id.'.'.$ext, true );
			if(!$upload){
				return redirect()->to('profile/index/change_pic')->with('notifyError', 'Server Error Occured while Uploading Image !');
			}

			(new UserModel)->update($id, ['img_type' => $ext]);

			model('App\Models\ActivityLogModel')->add("User #$id Updated his/her Profile Image.");

			return redirect()->to('profile/index/change_pic')->with('notifySuccess', 'Profile Image has been Updated Successfully');

		}

		return redirect()->to('profile/index/change_pic')->with('notifyError', 'Server Error Occured while Uploading Image !');

	}

	public function deleteAccount()
	{
		$id = logged('id');
		if (!$id) return redirect()->to('login');

		$userModel = new UserModel();
		
		// 1. Audit Log before deletion
		model('App\Models\ActivityLogModel')->add("User #$id requested PERMANENT ACCOUNT DELETION");

		// 2. Perform deactivation
		// We mark as status=0 and role=3 (standard parent)
		$userModel->update($id, [
			'status' => 0, // Inactive/Banned
			'role' => 3,   // Back to parent/standard
		]);

		// 3. Deactivate all listings by this provider
		$listingModel = new \App\Models\ListingModel();
		$listingModel->where('provider_id', $id)->set(['status' => 'inactive'])->update();

		// 4. Logout
		session()->destroy();
		
		return redirect()->to('/')->with('notifySuccess', 'Your account and all your listings have been successfully deleted.');
	}

	public function change_language($code = '')
	{
		return redirect()->to(!empty($_REQUEST['back']) ? urldecode($_REQUEST['back']) : '/' )->setCookie('current_lang', $code, time()+86400*30);
	}


}

/* End of file Profile.php */
/* Location: ./application/controllers/Profile.php */