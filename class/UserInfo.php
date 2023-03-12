<?php

class UserInfo {

	private string $full_name;
	private string $iban;
	private string $bic;
	private array $changedFields;
	private int $created_by;
	private int $id;

	public function __construct($id, $full_name = "", $iban = "", $bic = "", $created_by = null) {
		$this->id = $id;
		$this->full_name = $full_name;
		$this->iban = $iban;
		$this->bic = $bic;
		$this->created_by = $created_by;
		$this->changedFields = array(
			'full_name' => false,
			'iban' => false,
			'bic' => false
		);
	}

	public function set_full_name($full_name) {
		$this->full_name = $full_name;
		$this->changedFields['full_name'] = true;
	}

	public function set_IBAN($iban) {
		$this->iban = $iban;
		$this->changedFields['iban'] = true;
	}

	public function set_BIC($bic) {
		$this->bic = $bic;
		$this->changedFields['bic'] = true;
	}

	public function get_full_name() {
		return $this->full_name;
	}

	public function get_IBAN() {
		return $this->iban;
	}

	public function get_BIC() {
		return $this->bic;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_created_by() {
		return $this->created_by;
	}

	public function getChangedFields() {
		return array_filter($this->changedFields);
	}

	public function as_array() {
		return array(
			'full_name' => $this->get_full_name(),
			'IBAN' => $this->get_IBAN(),
			'BIC' => $this->get_BIC()
		)
	}
}

?>