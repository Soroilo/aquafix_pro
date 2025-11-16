<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_model extends CI_Model {

    private $tabla = 'solicitudes';

    public function __construct() {
        parent::__construct();
    }

    public function crear($datos) {
        $datos['fecha_solicitud'] = date('Y-m-d H:i:s');
        $datos['estado'] = 'pendiente';
        $insert = $this->db->insert($this->tabla, $datos);
        if ($insert) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function obtener_por_id($id) {
        $this->db->select('s.*, 
                          CONCAT(c.nombre, " ", c.apellido) as nombre_cliente,
                          c.email as email_cliente,
                          c.telefono as telefono_cliente,
                          CONCAT(f.nombre, " ", f.apellido) as nombre_fontanero,
                          f.telefono as telefono_fontanero,
                          srv.nombre as nombre_servicio,
                          srv.precio_base,
                          srv.duracion_estimada');
        $this->db->from($this->tabla . ' s');
        $this->db->join('clientes c', 's.id_cliente = c.id_cliente');
        $this->db->join('fontaneros f', 's.id_fontanero = f.id_fontanero', 'left');
        $this->db->join('servicios srv', 's.id_servicio = srv.id_servicio');
        $this->db->where('s.id_solicitud', $id);
        return $this->db->get()->row();
    }

    public function listar_por_cliente($id_cliente, $filtros = array()) {
        $this->db->select('s.*,
                          s.id_fontanero,
                          CONCAT(f.nombre, " ", f.apellido) as nombre_fontanero,
                          srv.nombre as nombre_servicio,
                          srv.precio_base');
        $this->db->from($this->tabla . ' s');
        $this->db->join('fontaneros f', 's.id_fontanero = f.id_fontanero', 'left');
        $this->db->join('servicios srv', 's.id_servicio = srv.id_servicio');
        $this->db->where('s.id_cliente', $id_cliente);

        if (!empty($filtros['estado'])) {
            $this->db->where('s.estado', $filtros['estado']);
        }

        if (!empty($filtros['limit'])) {
            $this->db->limit($filtros['limit']);
        }

        $this->db->order_by('s.fecha_solicitud', 'DESC');
        return $this->db->get()->result();
    }

    public function listar_por_fontanero($id_fontanero, $filtros = array()) {
        $this->db->select('s.*, 
                          CONCAT(c.nombre, " ", c.apellido) as nombre_cliente,
                          c.telefono as telefono_cliente,
                          srv.nombre as nombre_servicio,
                          srv.precio_base');
        $this->db->from($this->tabla . ' s');
        $this->db->join('clientes c', 's.id_cliente = c.id_cliente');
        $this->db->join('servicios srv', 's.id_servicio = srv.id_servicio');
        $this->db->where('s.id_fontanero', $id_fontanero);
        
        if (!empty($filtros['estado'])) {
            $this->db->where('s.estado', $filtros['estado']);
        }
        
        $this->db->order_by('s.fecha_programada', 'ASC');
        return $this->db->get()->result();
    }

    public function listar_todas($filtros = array()) {
        $this->db->select('s.*, 
                          CONCAT(c.nombre, " ", c.apellido) as nombre_cliente,
                          CONCAT(f.nombre, " ", f.apellido) as nombre_fontanero,
                          srv.nombre as nombre_servicio');
        $this->db->from($this->tabla . ' s');
        $this->db->join('clientes c', 's.id_cliente = c.id_cliente');
        $this->db->join('fontaneros f', 's.id_fontanero = f.id_fontanero', 'left');
        $this->db->join('servicios srv', 's.id_servicio = srv.id_servicio');
        
        if (!empty($filtros['estado'])) {
            $this->db->where('s.estado', $filtros['estado']);
        }
        if (!empty($filtros['prioridad'])) {
            $this->db->where('s.prioridad', $filtros['prioridad']);
        }
        if (!empty($filtros['fecha_desde'])) {
            $this->db->where('s.fecha_solicitud >=', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $this->db->where('s.fecha_solicitud <=', $filtros['fecha_hasta']);
        }
        
        $this->db->order_by('s.fecha_solicitud', 'DESC');
        return $this->db->get()->result();
    }

    public function asignar_fontanero($id_solicitud, $id_fontanero, $fecha_programada) {
        $datos = array(
            'id_fontanero' => $id_fontanero,
            'fecha_programada' => $fecha_programada,
            'estado' => 'asignada'
        );
        return $this->db->where('id_solicitud', $id_solicitud)
                        ->update($this->tabla, $datos);
    }

    public function actualizar_estado($id_solicitud, $estado) {
        return $this->db->where('id_solicitud', $id_solicitud)
                        ->update($this->tabla, array('estado' => $estado));
    }

    public function cancelar($id_solicitud) {
        return $this->actualizar_estado($id_solicitud, 'cancelada');
    }

    public function completar($id_solicitud) {
        return $this->actualizar_estado($id_solicitud, 'completada');
    }

    public function obtener_pendientes() {
        $this->db->select('s.*, 
                          CONCAT(c.nombre, " ", c.apellido) as nombre_cliente,
                          c.telefono as telefono_cliente,
                          srv.nombre as nombre_servicio');
        $this->db->from($this->tabla . ' s');
        $this->db->join('clientes c', 's.id_cliente = c.id_cliente');
        $this->db->join('servicios srv', 's.id_servicio = srv.id_servicio');
        $this->db->where('s.estado', 'pendiente');
        $this->db->order_by('s.prioridad', 'DESC');
        $this->db->order_by('s.fecha_solicitud', 'ASC');
        return $this->db->get()->result();
    }

    public function contar_por_estado($estado = null) {
        if ($estado) {
            $this->db->where('estado', $estado);
        }
        return $this->db->count_all_results($this->tabla);
    }

    public function obtener_estadisticas() {
        $this->db->select('
            COUNT(*) as total,
            SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = "asignada" THEN 1 ELSE 0 END) as asignadas,
            SUM(CASE WHEN estado = "en_proceso" THEN 1 ELSE 0 END) as en_proceso,
            SUM(CASE WHEN estado = "completada" THEN 1 ELSE 0 END) as completadas,
            SUM(CASE WHEN estado = "cancelada" THEN 1 ELSE 0 END) as canceladas
        ');
        return $this->db->get($this->tabla)->row();
    }
}
