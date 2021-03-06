<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Helper as H;

/**
 * Класс-сущность  документ   оприходование  с  производства
 *
 */
class ProdReceipt extends Document
{

    public function generateReport() {


        $i = 1;

        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "itemcode" => $value['item_code'],
                "quantity" => $value['quantity'],
                "price" => $value['price'],
                "amount" => $value['amount']
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "total" => $this->headerdata["total"]
        );


        $report = new \App\Report('prodreceipt.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
        $types = array();
        $common = \App\System::getOptions("common");

        //аналитика
        foreach ($this->detaildata as $row) {
            $stock = \App\Entity\Stock::getStock($this->headerdata['store'], $row['item_id'], $row['price'], true);


            $sc = new Entry($this->document_id, $row['amount'], $row['quantity']);
            $sc->setStock($stock->stock_id);

            $sc->save();
        }

        //$total = $this->headerdata['total'];

        return true;
    }

    public function getRelationBased() {
        $list = array();

        return $list;
    }

}
