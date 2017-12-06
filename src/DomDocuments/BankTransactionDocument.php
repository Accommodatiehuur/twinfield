<?php

namespace PhpTwinfield\DomDocuments;

use PhpTwinfield\BankTransaction;
use PhpTwinfield\Transactions\BankTransactionLine;

/**
 * Class BankTransactionDocument
 * @package PhpTwinfield\DomDocuments
 *
 * @link https://c3.twinfield.com/webservices/documentation/#/ApiReference/Transactions/BankTransactions
 */
class BankTransactionDocument extends BaseDocument
{
    /**
     * Multiple transactions can be created at once by enclosing them by the transactions element.
     *
     * @return string
     */
    final protected function getRootTagName(): string
    {
        return "transactions";
    }

    public function addBankTransaction(BankTransaction $bankTransaction): void
    {
        $transaction = $this->createElement("transaction");

        $transaction->appendChild(new \DOMAttr("destiny", $bankTransaction->getDestiny()));

        if ($bankTransaction->isAutoBalanceVat() !== null) {
            $transaction->appendChild(
                $this->createBooleanAttribute("autobalancevat", $bankTransaction->isAutoBalanceVat())
            );
        }

        $header = $this->createElement("header");
        $header->appendChild($this->createElement("office", $bankTransaction->getOffice()->getCode()));

        if ($bankTransaction->getNumber() !== null) {
            $header->appendChild($this->createElement("number", $bankTransaction->getNumber()));
        }

        if ($bankTransaction->getPeriod()) {
            $header->appendChild($this->createElement("period", $bankTransaction->getPeriod()));
        }

        $this->appendStartCloseValues($header, $bankTransaction);

        $this->appendFreeTextFields($header, $bankTransaction);
        $transaction->appendChild($header);

        $transactions = $this->createElement("transactions");
        $transaction->appendChild($transactions);

        foreach ($bankTransaction->getTransactions() as $line) {
            $transactions->appendChild($this->createTransactionLineElement($line));
        }

        $this->rootElement->appendChild($transaction);
    }

    protected function createTransactionLineElement(BankTransactionLine\Base $line): \DOMElement
    {
        $transaction = $this->createElement("transaction");
        $transaction->appendChild(new \DOMAttr("type", $line->getType()));
        $transaction->appendChild(new \DOMAttr("id", $line->getId()));

        if ($line->getDim1() !== null) {
            $transaction->appendChild($this->createElement("dim1", $line->getDim1()));
        }

        if ($line->getDim2() !== null) {
            $transaction->appendChild($this->createElement("dim2", $line->getDim2()));
        }

        if ($line->getDim3() !== null) {
            $transaction->appendChild($this->createElement("dim3", $line->getDim3()));
        }

        $this->appendValueValues($transaction, $line);



        return $transaction;

    }
}