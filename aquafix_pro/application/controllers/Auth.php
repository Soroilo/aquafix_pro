<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Cliente_model');
        $this->load->model('Fontanero_model');
    }
    public function register() {
    if ($this->session->userdata('logged_in')) {
        $tipo = $this->session->userdata('user_type');
        redirect($tipo . '/dashboard');
        return;
    }

    if ($this->input->method() === 'post') {
        header('Content-Type: application/json');
        
        $nombre = $this->input->post('nombre');
        $apellido = $this->input->post('apellido');
        $email = $this->input->post('email');
        $telefono = $this->input->post('telefono');
        $direccion = $this->input->post('direccion');
        $password = $this->input->post('password');
        $tipo = $this->input->post('tipo_usuario');

        if (!$nombre || !$apellido || !$email || !$telefono || !$password || !$tipo) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Complete todos los campos obligatorios'
            ));
            return;
        }

        $datos = array(
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'telefono' => $telefono,
            'password' => $password  // Los modelos lo hashearán con password_hash
        );

        if ($tipo === 'cliente') {
            $datos['direccion'] = $direccion;
            $datos['activo'] = TRUE;
            $resultado = $this->Cliente_model->crear($datos);
        } else if ($tipo === 'fontanero') {
            $datos['especialidad'] = $this->input->post('especialidad');
            $datos['disponible'] = TRUE;
            $datos['rating_promedio'] = 0;
            $datos['servicios_completados'] = 0;
            $resultado = $this->Fontanero_model->crear($datos);
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Tipo de usuario no válido'
            ));
            return;
        }

        if ($resultado) {
            echo json_encode(array(
                'success' => true,
                'message' => 'Registro exitoso. Redirigiendo al login...',
                'redirect' => base_url('login')
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al registrar. El email podría estar en uso.'
            ));
        }
        return;
    }

    $data['title'] = 'Registro';
    $this->load->view('templates/header', $data);
    $this->load->view('auth/register');
    $this->load->view('templates/footer');
}

    public function login() {
        if ($this->session->userdata('logged_in')) {
            $tipo = $this->session->userdata('user_type');
            redirect($tipo . '/dashboard');
            return;
        }

        if ($this->input->method() === 'post') {
            header('Content-Type: application/json');
            
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $tipo = $this->input->post('tipo_usuario');

            if (!$email || !$password || !$tipo) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Complete todos los campos'
                ));
                return;
            }

            $usuario = null;
            $id_key = '';
            
            // Validar según tipo de usuario
            if ($tipo === 'cliente') {
                $usuario = $this->Cliente_model->verificar_credenciales($email, $password);
                $id_key = 'id_cliente';
            } elseif ($tipo === 'fontanero') {
                $usuario = $this->Fontanero_model->verificar_credenciales($email, $password);
                $id_key = 'id_fontanero';
            } elseif ($tipo === 'admin') {
                // Validar administrador directamente en la base de datos
                $this->db->where('email', $email);
                $this->db->where('password', md5($password));
                $usuario = $this->db->get('administradores')->row();
                $id_key = 'id_admin';
            }

            if ($usuario) {
                $session_data = array(
                    'user_id' => $usuario->$id_key,
                    'user_email' => $usuario->email,
                    'user_name' => $usuario->nombre . ' ' . $usuario->apellido,
                    'user_type' => $tipo,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session_data);

                echo json_encode(array(
                    'success' => true,
                    'message' => 'Login exitoso',
                    'redirect' => base_url($tipo . '/dashboard')
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Email o contraseña incorrectos'
                ));
            }
            return;
        }

        $data['title'] = 'Iniciar Sesión';
        $this->load->view('templates/header', $data);
        $this->load->view('auth/login');
        $this->load->view('templates/footer');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }
}