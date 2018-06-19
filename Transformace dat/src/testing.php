<?php
/** Asserts that...
 * @param mixed
 * @return TestingSubject
 */
function assertThat($actual) {
	return new TestingSubject($actual);
}

class TestingSubject {
	private $actual;
	
	function __construct($actual) {
		$this->actual = $actual;
	}
	
	/** ... is equal to.
	 * @param mixed
	 * @throws Exception If not equal.
	 */
	function isEqualTo($expected) {
		if ($this->actual !== $expected) {
			throw new Exception("$this->actual !== $expected");
		}
	}
	
	/** ... contains match.
	 * @param string
	 * @throws Exception If does not contain.
	 */
	function containsMatch($pattern) {
		if (!preg_match("($pattern)", $this->actual)) {
			throw new Exception("$this->actual does not contain $pattern");
		}
	}
}
