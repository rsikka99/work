<?php

class Quotegen_Form_QuoteSetting extends EasyBib_Form {
	
	public function init() {
		// Set the method for the display form to POST
		$this->setMethod ( 'POST' );
		/**
		 * Add class to form for label alignment
		 *
		 * - Vertical .form-vertical (not required)	Stacked, left-aligned labels
		 * over controls (default)
		 * - Inline .form-inline Left-aligned label and inline-block controls
		 * for compact style
		 * - Search .form-search Extra-rounded text input for a typical search
		 * aesthetic
		 * - Horizontal .form-horizontal
		 *
		 * Use .form-horizontal to have same experience as with Bootstrap v1!
		 */
		$this->setAttrib ( 'class', 'form-horizontal' );
		
		$this->addElement ( 'text', 'pageCoverageMonochrome', array (
				'label' => 'Page Coverage Mono:',
				'required' => true,
				'class' => 'span1',
				'filters' => array (
						'StringTrim',
						'StripTags' 
				),
				'validators' => array (
						array (
								'validator' => 'StringLength',
								'options' => array (
										1,
										100 
								) 
						) 
				) 
		) );
		
		$this->addElement ( 'text', 'pageCoverageColor', array (
				'label' => 'Page Coverage Color:',
				'required' => true,
				'class' => 'span1',
				'filters' => array (
						'StringTrim',
						'StripTags' 
				),
				'validators' => array (
						array (
								'validator' => 'StringLength',
								'options' => array (
										1,
										100 
								) 
						) 
				) 
		) );
		
		$this->addElement ( 'text', 'deviceMargin', array (
				'label' => 'Device Margin:',
				'required' => true,
				'class' => 'span1',
				'filters' => array (
						'StringTrim',
						'StripTags' 
				),
				'validators' => array (
						array (
								'validator' => 'StringLength',
								'options' => array (
										1,
										100 
								) 
						) 
				) 
		) );
		
		$this->addElement ( 'text', 'pageMargin', array (
				'label' => 'Page Margin:',
				'required' => true,
				'class' => 'span1',
				'filters' => array (
						'StringTrim',
						'StripTags' 
				),
				'validators' => array (
						array (
								'validator' => 'StringLength',
								'options' => array (
										1,
										100 
								) 
						) 
				) 
		) );
		
		$this->addElement ( 'text', 'tonerPreference', array (
				'label' => 'Toner Preference:',
				'required' => true,
				'class' => 'span1',
				'filters' => array (
						'StringTrim',
						'StripTags' 
				),
				'validators' => array (
						array (
								'validator' => 'StringLength',
								'options' => array (
										1,
										100 
								) 
						) 
				) 
		) );
		
		
		// Add the submit button
		$this->addElement ( 'submit', 'submit', array (
				'ignore' => true,
				'label' => 'Save' 
		) );
		
		// Add the cancel button
		$this->addElement ( 'submit', 'cancel', array (
				'ignore' => true,
				'label' => 'Cancel' 
		) );
		
		EasyBib_Form_Decorator::setFormDecorator ( $this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel' );
	}
}
