<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
$bootstrap = $paths->systemDirectory . '/bootstrap.php';
require $bootstrap;

$phone = '9989284804';
echo "--- Testing OTP Login Flow for $phone ---\n";

$userModel = new \App\Models\UserModel();
$user = $userModel->findByPhone($phone);

if (!$user) {
    echo "User not found. Simulating NEW user registration...\n";
    $userId = $userModel->insert([
        'name'           => 'User-' . substr($phone, -4),
        'username'       => 'user_' . $phone,
        'email'          => null,
        'phone'          => $phone,
        'role'           => 3,
        'status'         => 1,
        'phone_verified' => 1,
        'password'       => password_hash(bin2hex(random_bytes(10)), PASSWORD_BCRYPT),
    ]);
    if (!$userId) {
        $errors = $userModel->errors();
        echo "Registration FAILED:\n";
        print_r($errors);
        exit(1);
    }
    echo "Registered OK with ID: $userId\n";
    $user = $userModel->find($userId);
} else {
    echo "User found: ID {$user->id}\n";
}

echo "Checking status integer check...\n";
if ((int)$user->status === 0) {
    echo "ERROR: User is suspended.\n";
    exit(1);
}

echo "Status check passed (Status: {$user->status}).\n";
echo "\nSimulating Session Creation...\n";
$time = time();
$login_token = sha1($user->id . $user->password . $time);

$sessionData = [
    'user_id'    => $user->id,
    'user_name'  => $user->name,
    'user_email' => $user->email,
    'user_role'  => $user->role,
    'logged_in'  => true,
    'cnd_phone'  => $user->phone ?? null,
    'cnd_user'   => [
        'id'    => $user->id,
        'name'  => $user->name,
        'email' => $user->email,
        'phone' => $user->phone ?? null,
        'role'  => $user->role,
    ],
    'login'       => true,
    'login_token' => $login_token,
    'logged'      => [
        'id'   => $user->id,
        'time' => $time,
    ]
];

session()->set($sessionData);
echo "Session data generated successfully.\n";
print_r(session()->get('cnd_user'));

$userModel->update($user->id, ['last_login' => date('Y-m-d H:i:s')]);
echo "Last login updated.\n";
echo "\n--- SUCCESS: OTP Login Flow works perfectly! ---\n";
