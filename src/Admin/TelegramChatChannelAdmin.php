<?php

declare(strict_types=1);

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use App\BotMan\Drivers\Telegram\TelegramDriver;
use App\Entity\TelegramChat;
use App\Entity\TelegramChatChannel;
use App\Form\DataMapper\GenericDataMapper;
use BotMan\BotMan\BotMan;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\Chat;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\HttpFoundation\Response;

class TelegramChatChannelAdmin extends AbstractAdmin
{
    /**
     * @var BotMan
     */
    private $botman;

    public function setBotman(BotMan $botMan): void
    {
        $this->botman = $botMan;
    }

    public function getNewInstance(): ?TelegramChat
    {
        return null;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', null, [
            ])
            ->add('active', null, [
                'required' => false,
            ])
        ;

        $form
            ->getFormBuilder()
            ->setEmptyData(null)
            ->setDataMapper(new GenericDataMapper(TelegramChatChannel::class));
    }

    /**
     * @param mixed $object
     *
     * @throws \BotMan\BotMan\Exceptions\Core\BadMethodCallException
     */
    public function prePersist($object): void
    {
        $this->botman->loadDriver(TelegramDriver::class);

        /** @var Response $response */
        $response = $this->botman->sendRequest('getChat', [
            'chat_id' => '@' . $object->getId(),
        ]);
        $response = \json_decode($response->getContent(), true);

        if (
            !array_key_exists('ok', $response) ||
            true !== $response['ok'] ||
            'channel' !== $response['result']['type']

        ) {
            throw new \RuntimeException(sprintf(
                'Channel `%s` does not found. Error: %s',
                $object->getId(),
                $response['description'] ?? ''
            ));
        }

        $chat = Chat::fromPayload($response['result']);

        $object->setId((string) $chat->getId());
        $object->setTitle($chat->getTitle());
        $object->setUsername($chat->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('username', null, [
            ])
            ->add('title', null, [
            ])
            ->add('active', null, [
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('username')
            ->add('active')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('username')
            ->add('title')
            ->add('active')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('edit');
    }
}
