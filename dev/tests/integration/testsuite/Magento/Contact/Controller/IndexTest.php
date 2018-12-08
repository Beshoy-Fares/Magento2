<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Contact\Controller;

<<<<<<< HEAD
use Zend\Http\Request;
=======
use Magento\Framework\App\Request\Http as HttpRequest;
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

/**
 * Contact index controller test
 */
class IndexTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * Test contacting.
     */
    public function testPostAction()
    {
        $params = [
            'name' => 'customer name',
            'comment' => 'comment',
            'email' => 'user@example.com',
            'hideit' => '',
        ];
<<<<<<< HEAD
        $this->getRequest()->setPostValue($params);
        $this->getRequest()->setMethod(Request::METHOD_POST);
=======
        $this->getRequest()->setPostValue($params)->setMethod(HttpRequest::METHOD_POST);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

        $this->dispatch('contact/index/post');
        $this->assertRedirect($this->stringContains('contact/index'));
        $this->assertSessionMessages(
            $this->contains(
                "Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon."
            ),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * Test validation.
     *
     * @param array $params For Request.
     * @param string $expectedMessage Expected response.
     *
     * @dataProvider dataInvalidPostAction
     */
    public function testInvalidPostAction($params, $expectedMessage)
    {
<<<<<<< HEAD
        $this->getRequest()->setPostValue($params);
        $this->getRequest()->setMethod(Request::METHOD_POST);
=======
        $this->getRequest()->setPostValue($params)->setMethod(HttpRequest::METHOD_POST);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

        $this->dispatch('contact/index/post');
        $this->assertRedirect($this->stringContains('contact/index'));
        $this->assertSessionMessages(
            $this->contains($expectedMessage),
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @return array
     */
    public static function dataInvalidPostAction()
    {
        return [
            'missing_comment' => [
                'params' => [
                    'name' => 'customer name',
                    'comment' => '',
                    'email' => 'user@example.com',
                    'hideit' => '',
                ],
                'expectedMessage' => "Enter the comment and try again.",
            ],
            'missing_name' => [
                'params' => [
                    'name' => '',
                    'comment' => 'customer comment',
                    'email' => 'user@example.com',
                    'hideit' => '',
                ],
                'expectedMessage' => "Enter the Name and try again.",
            ],
            'invalid_email' => [
                'params' => [
                    'name' => 'customer name',
                    'comment' => 'customer comment',
                    'email' => 'invalidemail',
                    'hideit' => '',
                ],
                'expectedMessage' => "The email address is invalid. Verify the email address and try again.",
            ],
        ];
    }
}
