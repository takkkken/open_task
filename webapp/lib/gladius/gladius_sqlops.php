<?php if(!defined('GLADIUS_HI_VER')){header('Status: 404 Not Found');die;}
## Gladius Database Driver for ADODB lite
# @author legolas558
# @version 0.7
# Licensed under GNU General Public License (GPL)
#
#
# Operator classes

// class begin_expr was moved to gladius.php

function equ_comp(&$lh, &$rh) {
	if (gettype($lh) != gettype($rh))
		return (strcmp($lh, $rh) == 0);
	return ($lh == $rh);
}


function sql_regex($m) {
	if (strlen($m[0])==3) {
		if (($m[0][0]=='\\') and
			($m[0]{1}=='\\')
		)
			return '%';
		return $m[0][0].$m[0]{1}.'.*?';
	}
	return '.*?';
}

class expr extends begin_expr {

var $op;
var $left;
var $right;


	function expr($left, $right, $op) {
		$this->left = $left;
		$this->right = $right;
		$this->op = $op;
		$this->_link($left);
	}

	function _evaluate() {

		//TODO: enforce correct automatic type casting as SQL92 states

/*		// automatic casting, is it ok for SQL92?
		if (is_null($lh)) {
			switch(gettype($rh)) {
				case 'string':
					$lh = '';
					break;
				case 'double':
					$lh = (double)0;
					break;
				case 'integer':
					$lh = (integer)0;
					break;
			}
		}
		if (is_null($rh)) {
			switch(gettype($lh)) {
				case 'string':
					$rh = '';
					break;
				case 'double':
					$rh = (double)0;
					break;
				case 'integer':
					$rh = (integer)0;
					break;
			}
		}

		if (gettype($rh) !== gettype($lh)) {
			$this->_error(_G__TYPE_MISMATCH, gettype($rh), gettype($lh));
			return null;
		}	*/

/*// for debugging
		global $flagg;
		if (@$flagg) {
		$lh = $this->left->evaluate();
		$rh = $this->right->evaluate();

		echo '<b>EXPR</b> (('.get_class($this->left);
		if (get_class($this->left)=='field')
			echo ' "'.$this->left->content->name.'" ) '.$this->left->content->value;
		else
			echo ') '.$this->left->content;
		echo ' '.$this->op.' ('.get_class($this->right);
		if (get_class($this->right)=='field')
			echo ' "'.$this->right->content->name.'" ) '.$this->right->content->value;
		else
			echo ') '.$this->right->content;
		}
		/// .*/

		switch ($this->op) {
			case '=':
				return $this->left->equal($this->right->evaluate());
			case '<>':
				return !$this->left->equal($this->right->evaluate());
			case '>=':
				return $this->left->gequal($this->right->evaluate());
			case '<=':
				return $this->left->lequal($this->right->evaluate());
			case '>':
				return $this->left->greater($this->right->evaluate());
			case '<':
				return $this->left->lesser($this->right->evaluate());
			case 'nlike': // needs testing
				return !$this->left->like($this->right->evaluate());
			case 'like':
				return $this->left->like($this->right->evaluate());
			default:
				die('<br>GRTE: "'.$this->op.'" not handled');
		}
	}

}

class in_op extends begin_expr {
	var $values;
	var $hand;

	function in_op(&$hand, &$values) {
		$this->hand =& $hand;
		$this->values =& $values;
	}

	function _evaluate() {
		$val = $this->hand->evaluate();
		return in_array($this->hand->evaluate(), $this->values);
	}

}

class nin_op extends in_op {

	function nin_op(&$hand, &$values) {
		$this->hand =& $hand;
		$this->values =& $values;
	}

	function _evaluate() {
		return !parent::_evaluate();
	}

}

class arith_expr extends begin_expr {
	var $op;
	var $left;
	var $right;

	function arith_expr($l, $r, $op) {
		$this->left = $l;
		$this->right = $r;
		$this->op = $op;
	}

	function test() {
		die('virtual method called');
	}

	function evaluate() {
		$lv = $this->left->evaluate();
		$rv = $this->right->evaluate();
		$bf = debug_backtrace();
		switch ($this->op) {
			case '+':
				return $lv + $rv;
			case '-':
				return $lv - $rv;
			case '*':
				return $lv * $rv;
			case '/':
				return $lv / $v;
			case '%':
				return $lv % $v;
		}
	}
}

class value extends Gladius_Core {

var $content;

	function value($content, &$par) {
		$this->content = $content;
		$this->_link($par);
	}
	
	function evaluate() {
		return $this->content;
	}

	function equal($val) {
		return ($this->content == $val);
	}
	
	function like($rh) {
		$lh = $this->evaluate();
		if (gettype($rh) !== 'string') {
			$this->_error(_G__TYPE_MISMATCH, gettype($lh), gettype($rh));
			return false;
		}
		settype($lh, 'string');		

		$rh = '/'.preg_replace_callback('/.?.?%/', 'sql_regex', preg_quote($rh)).'$/iA';
		return preg_match($rh, $lh);
	}

}

class integer_value extends value {
	function integer_value($number, &$par) {
		$this->content = $number;
		$this->_link($par);
	}

	function gequal($val) {
		return ($this->content >= $val);
	}

	function lequal($val) {
		return ($this->content <= $val);
	}
	
	function greater($val) {
		return ($this->content > $val);
	}

	function lesser($val) {
		return ($this->content < $val);
	}

}

class double_value extends integer_value { }

class string_value extends value {
	function string_value($string, &$par) {
		$this->content = $string;
		$this->_link($par);
	}

	function lesser($val) {
		return (strpos($this->content, $val)===strlen($this->content)-strlen($val));
	}
	
	function lequal($val) { // bad implementation here?
		return $this->lesser($val);
	}

	function greater($val) {
		return (strpos($this->content, $val)===0);
	}

	function gequal($val) { // bad implementation here?
		return $this->greater($val);
	}
	
}

class varchar_value extends string_value { }

class nfield extends integer_value {

	function nfield(&$fld, &$par) {
		$this->content =& $fld->value;
		$this->_link($par);
	}

}

class sfield extends string_value {

	function sfield(&$fld, &$par) {
		$this->content =& $fld->value;
		$this->_link($par);
	}

}


class func_value extends value {
	var $name;
	var $argument;

	function func($fn, &$par, &$subexpr) {
		$this->name = $fn;
		$this->argument =& $subexpr;
		$this->_link($par);
	}

	function evaluate() {
		die('GRTE: functions not yet supported');
	}
}

?>