<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Mail
 */


/**
 * Zend implementation for creating e-mails.
 *
 * @package MW
 * @subpackage Mail
 */
class MW_Mail_Message_Zend implements MW_Mail_Message_Interface
{
	private $_object;


	/**
	 * Initializes the message instance.
	 *
	 * @param Zend_Mail $object Zend mail object
	 */
	public function __construct( Zend_Mail $object )
	{
		$this->_object = $object;
	}


	/**
	 * Adds a source e-mail address of the message.
	 *
	 * @param string $email Source e-mail address
	 * @param string|null $name Name of the user sending the e-mail or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addFrom( $email, $name = null )
	{
		$this->_object->setFrom( $email, $name );
		return $this;
	}


	/**
	 * Adds a destination e-mail address of the target user mailbox.
	 *
	 * @param string $email Destination address of the target mailbox
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addTo( $email, $name = null )
	{
		$this->_object->addTo( $email, $name );
		return $this;
	}


	/**
	 * Adds a destination e-mail address for a copy of the message.
	 *
	 * @param string $email Destination address for a copy
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addCc( $email, $name = null )
	{
		$this->_object->addCc( $email, $name );
		return $this;
	}


	/**
	 * Adds a destination e-mail address for a hidden copy of the message.
	 *
	 * @param string $email Destination address for a hidden copy
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addBcc( $email, $name = null )
	{
		$this->_object->addBcc( $email, $name );
		return $this;
	}


	/**
	 * Adds the return e-mail address for the message.
	 *
	 * @param string $email E-mail address which should receive all replies
	 * @param string|null $name Name of the user which should receive all replies or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addReplyTo( $email, $name = null )
	{
		$this->_object->setReplyTo( $email, $name );
		return $this;
	}


	/**
	 * Sets the e-mail address and name of the sender of the message (higher precedence than "From").
	 *
	 * @param string $email Source e-mail address
	 * @param string|null $name Name of the user who sent the message or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setSender( $email, $name = null )
	{
		$this->_object->setFrom( $email, $name );
		return $this;
	}


	/**
	 * Sets the subject of the message.
	 *
	 * @param string $subject Subject of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setSubject( $subject )
	{
		$this->_object->setSubject( $subject );
		return $this;
	}


	/**
	 * Sets the text body of the message.
	 *
	 * @param string $message Text body of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setBody( $message )
	{
		$this->_object->setBodyText( $message );
		return $this;
	}


	/**
	 * Sets the HTML body of the message.
	 *
	 * @param string $message HTML body of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setBodyHtml( $message )
	{
		$this->_object->setBodyHtml( $message );
		return $this;
	}


	/**
	 * Adds an attachment to the message.
	 *
	 * @param string $data Binary or string @author nose
	 * @param string $mimetype Mime type of the attachment (e.g. "text/plain", "application/octet-stream", etc.)
	 * @param string|null $filename Name of the attached file (or null if inline disposition is used)
	 * @param string $disposition Type of the disposition ("attachment" or "inline")
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addAttachment( $data, $mimetype, $filename, $disposition = 'attachment' )
	{
		$enc = Zend_Mime::ENCODING_BASE64;
		$part = $this->_object->createAttachment( $data, $mimetype, $disposition, $enc, $filename );

		$this->_object->addAttachment( $part );
		return $this;
	}


	/**
	 * Returns the internal Zend mail object.
	 *
	 * @return Zend_Mail Zend mail object
	 */
	public function getObject()
	{
		return $this->_object;
	}


	/**
	 * Clones the internal objects.
	 */
	public function __clone()
	{
		$this->_object = clone $this->_object;
	}
}
