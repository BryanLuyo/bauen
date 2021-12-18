<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class BaseModel extends CI_Model {
    protected $table_name = '';
    protected $primary_key = '';

    protected $fillable = [];

    public function __construct() {
        parent::__construct();

        $this->load->database();
        //$this->load->helper('inflector');

        // Dinamycally set table_name and primary_key from descendants (in cases that the tables are not done with common sense)
        if (!$this->table_name) {
            $this->table_name = strtolower(plural(get_class($this)));
        }

        if (!$this->primary_key) {
            $this->primary_key = strtolower(plural(get_class($this)));
        }
    }


    public function find($id) {
        return $this->db->get_where($this->table_name, array($this->primary_key => $id))->row();
    }


    public function all($fields = '', $where = array(), $table_name = '', $limit = '', $order_by = '', $group_by = '') {
        $data = array();
        if ($fields != '') {
            $this->db->select($fields);
        }

        if (count($where)) {
            $this->db->where($where);
        }

        if ($table_name != '') {
            $this->table_name = $table_name;
        }

        if ($limit != '') {
            $this->db->limit($limit);
        }

        if ($order_by != '') {
            $this->db->order_by($order_by);
        }

        if ($group_by != '') {
            $this->db->group_by($group_by);
        }

        $Q = $this->db->get($this->table_name);

        if ($Q->num_rows() > 0) {
            foreach ($Q->result_array() as $row) {
                $data[] = $row;
            }
        }

        $Q->free_result();

        return $data;
    }


    public function insert($data) {
        //$data['date_created'] = $data['date_updated'] = date('Y-m-d H:i:s');
        //$data['created_from_ip'] = $data['updated_from_ip'] = $this->input->ip_address();

        $success = $this->db->insert($this->table_name, $data);
        if ($success) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    
    public function update($data, $id) {
        //$data['date_updated'] = date('Y-m-d H:i:s');
        //$data['updated_from_ip'] = $this->input->ip_address();

        $this->db->where($this->primary_key, $id);

        return $this->db->update($this->table_name, $data);
    }


    public function delete($id) {
        $this->db->where($this->primary_key, $id);

        return $this->db->delete($this->table_name);
    }
}
