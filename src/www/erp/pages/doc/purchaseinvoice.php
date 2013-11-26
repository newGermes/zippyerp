<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\Date;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use Zippy\Html\Panel;
use ZippyERP\System\Application as App;
use ZippyERP\System\System;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\ERP\Entity\Store;

/**
 * Страница  ввода  приходной  накладной
 */
class PurchaseInvoice extends \ZippyERP\ERP\Pages\Base
{

        public $_itemlist = array();
        private $_doc;

        public function __construct($docid = 0)
        {
                parent::__construct();

                $this->add(new Form('docform'));
                $this->docform->add(new TextInput('document_number'));
                $this->docform->add(new Date('created'))->setDate();
                $this->docform->add(new DropDownChoice('customer', Customer::findArray("customer_name", "")));
                $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "")));
                $this->docform->add(new TextInput('reference'));
                $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
                $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
                $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');

                $this->docform->add(new TextInput('nds'));
                $this->docform->add(new Label('total'));
                $this->add(new Form('editdetail'))->setVisible(false);
                $this->editdetail->add(new DropDownChoice('edititem', Item::findArray("itemname", "")));
                $this->editdetail->add(new TextInput('editquantity'))->setText("1");
                $this->editdetail->add(new TextInput('editprice'));
                $this->editdetail->add(new TextInput('editserial_number'));
                $this->editdetail->setSubmitHandler($this, 'saverowOnClick');
                $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');

                if ($docid > 0) {    //загружаем   содержимок  документа настраницу
                        $this->_doc = Document::load($docid);
                        $this->docform->document_number->setText($this->_doc->document_number);
                        $this->docform->reference->setText($this->_doc->headerdata['reference']);
                        $this->docform->nds->setText($this->_doc->headerdata['nds'] / 100);
                        $this->docform->created->setDate($this->_doc->document_date);
                        $this->docform->customer->setValue($this->_doc->headerdata['customer']);
                        $this->docform->store->setValue($this->_doc->headerdata['store']);

                        foreach ($this->_doc->detaildata as $item) {
                                $item = new Item($item);
                                $this->_itemlist[$item->item_id] = $item;
                        }
                } else {
                        $this->_doc = Document::create('PurchaseInvoice');
                }

                $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_itemlist')), $this, 'detailOnRow'))->Reload();
        }

        public function detailOnRow($row)
        {
                $item = $row->getDataItem();

                $row->add(new Label('item', $item->itemname));
                $row->add(new Label('measure', $item->measure_name));
                $row->add(new Label('serial_number', $item->serial_number));
                $row->add(new Label('quantity', $item->quantity));
                $row->add(new Label('price', number_format($item->price / 100, 2)));
                $row->add(new Label('amount', number_format($item->quantity * $item->price / 100, 2)));
                $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
        }

        public function deleteOnClick($sender)
        {
                $item = $sender->owner->getDataItem();
                // unset($this->_itemlist[$item->item_id]);

                $this->_itemlist = array_diff_key($this->_itemlist, array($item->item_id => $this->_itemlist[$item->item_id]));
                $this->docform->detail->Reload();
        }

        public function addrowOnClick($sender)
        {
                $this->editdetail->setVisible(true);
                $this->docform->setVisible(false);
        }

        public function saverowOnClick($sender)
        {
                $id = $this->editdetail->edititem->getValue();
                if ($id == 0) {
                        $this->setError("Не выбран товар");
                        return;
                }
                $item = Item::load($id);
                $item->quantity = $this->editdetail->editquantity->getText();
                $item->price = $this->editdetail->editprice->getText() * 100;
                $item->serial_number = $this->editdetail->editserial_number->getText();

                $this->_itemlist[$item->item_id] = $item;
                $this->editdetail->setVisible(false);
                $this->docform->setVisible(true);
                $this->docform->detail->Reload();

                //очищаем  форму
                $this->editdetail->edititem->setValue(0);
                $this->editdetail->editquantity->setText("1");
                $this->editdetail->editserial_number->setText("");
                $this->editdetail->editprice->setText("");
        }

        public function cancelrowOnClick($sender)
        {
                $this->editdetail->setVisible(false);
                $this->docform->setVisible(true);
        }

        public function savedocOnClick($sender)
        {
                if ($this->checkForm() == false) {
                        return;
                }

                $this->calcTotal();

                $this->_doc->headerdata = array(
                    'customer' => $this->docform->customer->getValue(),
                    'store' => $this->docform->store->getValue(),
                    'reference' => $this->docform->reference->getValue(),
                    'nds' => $this->docform->nds->getValue() * 100
                );
                $this->_doc->detaildata = array();
                foreach ($this->_itemlist as $item) {
                        $this->_doc->detaildata[] = $item->getData();
                }

                $this->_doc->amount = 100 * $this->docform->total->getText();
                $this->_doc->document_number = $this->docform->document_number->getText();
                $this->_doc->document_date = $this->docform->created->getDate();
                $this->_doc->save();
                App::Redirect("\\ZippyERP\\ERP\\Pages\\Register\\DocList");
        }

        /**
         * Расчет  итого
         * 
         */
        private function calcTotal()
        {
                $total = 0;
                foreach ($this->_itemlist as $item) {
                        $total = $total + $item->price / 100 * $item->quantity;
                }
                $this->docform->total->setText(number_format($total + $this->docform->nds->getText(), 2));
        }

        /**
         * Валидация   формы
         * 
         */
        private function checkForm()
        {

                if (count($this->_itemlist) == 0) {
                        $this->setError("Не введен ни один  товар");
                        return false;
                }
                if ($this->docform->customer->getValue() == 0) {
                        $this->setError("Не выбран  поставщик");
                        return false;
                }
                return true;
        }

        public function beforeRender()
        {
                parent::beforeRender();

                $this->calcTotal();
        }

        public function backtolistOnClick($sender)
        {
                App::Redirect("\\ZippyERP\\ERP\\Pages\\Register\\DocList");
        }

        /*
          События  жизненного  цикла  страницы, раскоментировать нужное
          public function beforeRequest(){
          parent::beforeRequest();

          }
          public function afterRequest(){
          parent::afterRequest();

          }
          public function beforeRender(){
          parent::beforeRender();

          }
          public function afterRender(){
          parent::afterRender();

          }
         */
}