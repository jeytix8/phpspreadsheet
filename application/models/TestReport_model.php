<?php

class TestReport_model extends CI_Model
{
    public function getRetailProducts(
        $customRequest = '',
        $reqSalon = '',
        $startDate = '',
        $endDate = '',
        $locationId = '',
        $itemId = ''
    ) {
        $this->db->select("
        phppos_sales_items.item_id,
        phppos_sales.location_id,
        phppos_locations.name as location_name,
        COALESCE(SUM(phppos_sales_items.quantity_purchased), 0) as no_of_units,
        phppos_items.name,
        COALESCE(SUM(phppos_sales_items.subtotal), 0) as total_take
    ");

        $this->db->from('phppos_sales_items');

        $this->db->join(
            'phppos_sales',
            'phppos_sales.sale_id = phppos_sales_items.sale_id
        AND phppos_sales.deleted = 0
        AND phppos_sales.suspended = 0'
        );

        $this->db->join(
            'phppos_items',
            'phppos_sales_items.item_id = phppos_items.item_id'
        );

        $this->db->join(
            'phppos_locations',
            'phppos_sales.location_id = phppos_locations.location_id'
        );

        $this->db->join(
            'phppos_categories cat',
            'phppos_items.category_id = cat.id'
        );

        $this->db->join(
            'phppos_categories cat2',
            'cat.parent_id = cat2.id',
            'left'
        );

        $this->db->where('phppos_sales_items.quantity_purchased !=', 0);

        $this->db->where("
            (
                TRIM(cat.name) = 'Retail'
                OR TRIM(cat2.name) = 'Retail'
            )
        ");

        if (!empty($reqSalon) && $customRequest == 'getUnits_TotalTakePerSalonFunction') {
            $this->db->where('phppos_locations.name', $reqSalon);
        }

        if ($customRequest == 'groupByProductName') {
            $this->db->group_by([   //  Group by Name, Products without categorizing with salon
                'phppos_sales_items.item_id',
                'phppos_items.name'
            ]);
        } else {
            $this->db->group_by([   //  Default: Group by Salon
                'phppos_sales.location_id',
                'phppos_sales_items.item_id',
                'phppos_items.name'
            ]);

            $this->db->order_by('phppos_sales.location_id', 'ASC');
        }

        if (!empty($startDate) && !empty($endDate)) {
            $this->db->where('DATE(phppos_sales.sale_time) >=', $startDate);
            $this->db->where('DATE(phppos_sales.sale_time) <=', $endDate);
        }
        if (!empty($itemId)) {
            $this->db->where('phppos_sales_items.item_id', $itemId);
        }
        if (!empty($locationId)) {
            $this->db->where('phppos_sales.location_id', $locationId);
        }

        $this->db->order_by('phppos_items.name', 'ASC');

        return $this->db->get()->result_array();
    }

    public function getUnits_TotalTakePerSalon($salon = '', $start_date = '', $end_date = '', $location_id = '', $item_id = '')
    {
        $dataPerSalon = $this->getRetailProducts('getUnits_TotalTakePerSalonFunction', $salon, $start_date, $end_date, $location_id, $item_id);

        $no_of_units = 0;
        $totalTake = 0;

        foreach ($dataPerSalon as $data) {
            $no_of_units += $data['no_of_units'];
            $totalTake += $data['total_take'];
        }

        return [
            'no_of_units' => $no_of_units,
            'total_take' => $totalTake
        ];
    }

    public function getLocations()
    {
        return $this->db
            ->select('location_id, name')
            ->from('phppos_locations')
            ->order_by('name', 'ASC')
            ->get()
            ->result_array();
    }

    public function getProducts()
    {
        return $this->db
            ->select('item_id, name')
            ->from('phppos_items')
            ->where('name IS NOT NULL', null, false)
            ->where('name !=', '')
            ->order_by('name', 'ASC')
            ->get()
            ->result_array();
    }
}
