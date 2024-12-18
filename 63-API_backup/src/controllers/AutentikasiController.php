<?php
namespace MyApp\Controllers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MyApp\Core\BaseController;

class AutentikasiController extends BaseController
{

  private $authModel;
  private const MESSAGE = [
    'email' => [
      'required' => 'Email harus diisi!',
      'email' => 'Email tidak valid!',
    ],
    'password' => [
      'required' => 'Password harus diisi!',
      'secure' => 'Password minimal 8 karakter, kombinasi huruf besar, huruf kecil dan karakter khusus!',
    ]
  ];
  public function __construct()
  {
    $this->authModel = $this->model('MyApp\Models\AutentikasiModel');
  }

  public function register()
  {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
      $data = [
        'status' => '400',
        'error' => '400',
        'message' => 'Data tidak valid!',
        'data' => $data
      ];
      $this->view('template/header');
      header('HTTP/1.0 400 Bad Request');
      echo json_encode($data);
      exit();
    }
    $field = [
      'email' => 'string | required | email | unique: autentikasi, email',
      'password' => 'string | required | secure',
    ];
    [$inputs, $errors] = $this->filter($data, $field, self::MESSAGE);
    if ($errors) {
      $data = [
        'status' => '400',
        'error' => '400',
        'message' => $errors,
        'data' => $inputs
      ];
      $this->view('template/header');
      header('HTTP/1.0 400 Bad Request');
      echo json_encode($data);
      exit();
    } else {
      $proc = $this->authModel->insert($data);
      if ($proc->rowCount() > 0) {
        $data = [
          'status' => '201',
          'error' => null,
          'message' => "Data ditambahkan " . $proc->rowCount() . " baris",
          'data' => $inputs
        ];
        $this->view('template/header');
        header('HTTP/1.0 201 OK');
        echo json_encode($data);
      } else {
        $data = [
          'status' => '400',
          'error' => '400',
          'message' => 'Data gagal ditambahkan',
          'data' => null
        ];
        $this->view('template/header');
        header('HTTP/1.0 400 Bad Request');
        echo json_encode($data);
      }
    }
  }

  public function login()
  {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
      $data = [
        'status' => '400',
        'error' => '400',
        'message' => 'Data tidak valid!',
        'data' => $data
      ];
      $this->view('template/header');
      header('HTTP/1.0 400 Bad Request');
      echo json_encode($data);
      exit();
    }
    $field = [
      'email' => 'string | required | email',
      'password' => 'string | required',
    ];
    [$inputs, $errors] = $this->filter($data, $field, self::MESSAGE);
    if ($errors) {
      $data = [
        'status' => '400',
        'error' => '400',
        'message' => $errors,
        'data' => $inputs
      ];
      $this->view('template/header');
      header('HTTP/1.0 400 Bad Request');
      echo json_encode($data);
      exit();
    } else {
      $proc = $this->authModel->getByEmail($inputs['email']);
      if ($proc) {
        if (password_verify($inputs['password'], $proc['password'])) {
          $iat = time();
          $expired_time = $iat + getenv('JWT_TIME_TO_LIVE');
          $payload = [
            'email' => $inputs['email'],
            'iat' => $iat,
            'exp' => $expired_time
          ];
          $access_token = JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');
          $data = [
            'status' => '200',
            'error' => null,
            'message' => "Login berhasil",
            'data' => [
              'accessToken' => $access_token,
              'expiry' => date(DATE_ISO8601, $expired_time)
            ]
          ];
          // Ubah waktu kadaluarsa lebih lama, dalam kasus ini 1 jam
          $payload['exp'] = time() + (60 * 60);
          $refresh_token = JWT::encode($payload, getenv('REFRESH_TOKEN_SECRET'), 'HS256');

          // Simpan refresh token di http-only cookie
          setcookie("refreshToken", $refresh_token, $payload['exp'], "/", "", 0);

          $this->view('template/header');
          header('HTTP/1.0 200 OK');
          echo json_encode($data);
          exit();
        } else {
          $data = [
            'status' => '400',
            'error' => '400',
            'message' => 'Password anda salah!',
            'data' => $inputs
          ];
          $this->view('template/header');
          header('HTTP/1.0 400 Bad Request');
          echo json_encode($data);
          exit();
        }
      } else {
        $data = [
          'status' => '400',
          'error' => '400',
          'message' => 'Akun tidak ditemukan!',
          'data' => $inputs
        ];
        $this->view('template/header');
        header('HTTP/1.0 400 Bad Request');
        echo json_encode($data);
        exit();
      }
    }
  }

  public function refresh()
  {
    if (!isset($_COOKIE['refreshToken'])) {
      http_response_code(403);
      $data = [
        'status' => '403',
        'error' => '403',
        'message' => 'Data expire!',
        'data' => null
      ];
      $this->view('template/header');
      header('HTTP/1.0 403 Forbidden');
      echo json_encode($data);
      exit();
    }

    try {
      // Men-decode token. Dalam library ini juga sudah sekaligus memverfikasinya
      $refresh_payload = JWT::decode($_COOKIE['refreshToken'], new Key(getenv('REFRESH_TOKEN_SECRET'), 'HS256'));
      $iat = time();
      $expired_time = $iat + getenv('JWT_TIME_TO_LIVE');
      $payload = [
        'email' => $refresh_payload->email,
        'iat' => $iat,
        'exp' => $expired_time
      ];
      $access_token = JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');
      $data = [
        'status' => '200',
        'error' => null,
        'message' => "Login berhasil",
        'data' => [
          'accessToken' => $access_token,
          'expiry' => date(DATE_ISO8601, $expired_time)
        ]
      ];
      // Ubah waktu kadaluarsa lebih lama, dalam kasus ini 1 jam
      $payload['exp'] = time() + (60 * 60);
      $refresh_token = JWT::encode($payload, getenv('REFRESH_TOKEN_SECRET'), 'HS256');

      // Simpan refresh token di http-only cookie
      setcookie('refreshToken', $refresh_token, $payload['exp'], '', '', false, true);

      $this->view('template/header');
      header('HTTP/1.0 200 OK');
      echo json_encode($data);
      exit();
    } catch (Exception $e) {
      // Bagian ini akan jalan jika terdapat error saat JWT diverifikasi atau di-decode
      $data = [
        'status' => '401',
        'error' => '401',
        'message' => "Login Field",
        'data' => null
      ];
      $this->view('template/header');
      http_response_code(401);
      exit();
    }
  }
}