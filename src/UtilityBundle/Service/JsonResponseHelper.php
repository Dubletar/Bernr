<?php

namespace UtilityBundle\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class JsonResponseHelper
{
    const SUCCESS = 'success';
    const MESSAGE = 'message';
    const MESSAGE_CLASS = 'message_class';
    const ORIGINAL_CONTENT = 'original_content';
    const CODE_ID = 'code_id';
    const TITLE = 'title';
    const SECTION = 'section';
    const ORIGINAL_SECTION = 'original_section';
    const ORIGINAL_TITLE = 'original_title';
    const CONTENT = 'content';
    const CLEAN_CONTENT = 'clean_content';
    const EDITED_BY = 'edited_by';
    const EDITED_AT = 'edited_at';
    const PROPOSAL_ID = 'proposal_id';
    const IS_ON_PROPOSAL = 'is_on_proposal';
    const YEAR = 'year';
    const CHAPTER = 'chapter';
    const BOOK = 'book';
    const CODE_INTRODUCTION = 'code_introduction';
    const TABLE_GROUP = 'table_group';
    const TABLE_ID = 'table_id';
    const FOOTNOTE_TYPE = 'footnote_type';
    const FOOTNOTE_ID = 'footnote_id';
    const FIGURE_ID = 'figure_id';
    const FIGURE_NUMBER = 'figure_number';
    const AJAX_SUCCESS = 'ajax-success';
    const AJAX_FAILURE = 'ajax-failure';

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Serializer
     */
    protected $jms;

    /**
     * @var array
     */
    protected static $defaultHeaders = array('Content-Type' => 'application/json');

    /**
     * @param Translator $translator
     * @param Serializer $jms
     */
    public function __construct(TranslatorInterface $translator, Serializer $jms)
    {
        $this->translator = $translator;
        $this->jms = $jms;
    }

    /**
     * @param bool   $success
     * @param string $message
     * @param int    $status
     * @param array  $additionalData
     * @param array  $headers
     * @param string $translationDomain
     *
     * @return JsonResponse
     */
    public function createJsonResponse(
        $success,
        $message,
        $status = 200,
        $additionalData = array(),
        $headers = array(),
        $translationDomain = 'ajax'
    ) {
        $data = $this->buildData($success, $message, $additionalData, $translationDomain);

        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param mixed                     $data
     * @param bool                      $success
     * @param string                    $message
     * @param int                       $status
     * @param array                     $headers
     * @param string                    $translationDomain
     * @param null|SerializationContext $serializationContext
     *
     * @return Response
     */
    public function createJmsResponse(
        $data,
        $success = true,
        $message = '',
        $status = 200,
        $headers = array(),
        $translationDomain = 'ajax',
        SerializationContext $serializationContext = null
    ) {
        if ($serializationContext !== null) {
            $context = $serializationContext;
        } else {
            $context = new SerializationContext();
            $context->setSerializeNull(true);
        }

        $additionalData = array('data' => $data, 'timestamp' => time());
        $contentArray = $this->buildData($success, $message, $additionalData, $translationDomain);
        $content = $this->jms->serialize($contentArray, 'json', $context);

        $responseHeaders = array_merge(static::$defaultHeaders, $headers);

        return new Response($content, $status, $responseHeaders);
    }

    /**
     * @param bool   $success
     * @param string $message
     * @param array  $additionalData
     * @param string $translationDomain
     *
     * @return array
     */
    protected function buildData($success, $message, $additionalData, $translationDomain)
    {
        $data = array(
            static::SUCCESS => $success,
            static::MESSAGE => $this->translator->trans($message, array(), $translationDomain),
            static::MESSAGE_CLASS => $success ? static::AJAX_SUCCESS : static::AJAX_FAILURE,
        );

        return array_merge($data, $additionalData);
    }
}
