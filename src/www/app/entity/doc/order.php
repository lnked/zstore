<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Helper as H;
use App\Util;

/**
 * Класс-сущность  документ расходная  накладая
 *
 */
class Order extends \App\Entity\Doc\Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {

            if (isset($detail[$value['item_id']])) {
                $detail[$value['item_id']]['quantity'] += $value['quantity'];
            } else {
                $detail[] = array("no" => $i++,
                    "tovar_name" => $value['itemname'],
                    "tovar_code" => $value['item_code'],
                    "quantity" => $value['quantity'],
                    "price" => $value['price'],
                    "amount" => ($value['quantity'] ) * $value['price']
                );
            }
        }

        //$firm = \App\System::getOptions("common");

        //  $customer = \App\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
        
            "customername" => $this->headerdata["customer_name"],
            "phone" => $this->headerdata["phone"],
            "email" => $this->headerdata["email"],
            "delivery" => $this->headerdata["delivery_name"],
            "notes" => $this->notes,
            "document_number" => $this->document_number,
            "total" => $this->headerdata["total"]
        );
        if ($this->headerdata["delivery"] == 2 || $this->headerdata["delivery"] == 3) {
            $header['delivery'] = $header['delivery'] . '. по адресу: ' . $this->headerdata["address"];
        }
        $report = new \App\Report('order.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
         
        return true;
    }

    public function getRelationBased() {
        $list = array();
         $list['GoodsIssue'] = 'Расходная накладная';
        

        return $list;
    }

}
