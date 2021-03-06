<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Helper as H;
use App\Util;

/**
 * Класс-сущность  документ расходная  накладая
 *
 */
class GoodsIssue extends Document
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

        $firm = \App\System::getOptions("common");

        //  $customer = \App\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['firmname'],
            "customername" => $this->headerdata["customer_name"],
            "order" => strlen($this->headerdata["order"])>0 ? $this->headerdata["order"] : false,
            "document_number" => $this->document_number,
            "total" => $this->headerdata["total"],
            "summa" => Util::ucfirst(Util::money2str($this->headerdata["total"]))
        );

        $report = new \App\Report('goodsissue.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();

        foreach ($this->detaildata as $row) {

            $sc = new Entry($this->document_id, 0 - $row['amount'], 0 - $row['quantity']);
            $sc->setStock($row['stock_id']);
            $sc->setExtCode($row['price'] - $row['partion']); //Для АВС 
            if ($this->headerdata["customer"] > 0)
                $sc->setCustomer($this->headerdata["customer"]);
            $sc->save();
        }

        $conn->CompleteTrans();
        return true;
    }

    public function getRelationBased() {
        $list = array();
        $list['Warranty'] = 'Гарантийный талон';
        $list['ReturnIssue'] = 'Возвратная накладная';

        return $list;
    }

}
