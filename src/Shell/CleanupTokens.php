<?php

namespace OAuthServer\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\RepositoryInterface;
use InvalidArgumentException;
use OAuthServer\Model\Table\RevocableTokensTableInterface;

/**
 * Class CleanupTokens
 */
class CleanupTokens extends Shell
{
    /**
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->setDescription(__('Delete expired/revoked token from database'));

        $parser->addOption('expired', [
            'help' => __('Delete expired token.'),
            'boolean' => true,
            'default' => false,
        ]);

        $parser->addOption('revoked', [
            'help' => __('Delete revoked token.'),
            'boolean' => true,
            'default' => false,
        ]);

        $parser->addSubcommand('All', ['help' => __('delete all invalid token/codes')]);
        $parser->addSubcommand('AccessTokens', ['help' => __('delete invalid access tokens')]);
        $parser->addSubcommand('AuthCodes', ['help' => __('delete invalid authorization codes')]);
        $parser->addSubcommand('RefreshTokens', ['help' => __('delete invalid refresh tokens')]);

        return $parser;
    }

    /**
     * @return bool|int|void|null
     */
    public function main()
    {
        $this->all();
    }

    /**
     * @return void
     */
    public function all()
    {
        $this->accessTokens();
        $this->authCodes();
        $this->refreshTokens();
    }

    /**
     * @return void
     */
    public function accessTokens()
    {
        $table = $this->loadModel('OAuthServer.AccessTokens');

        $this->process($table);
    }

    /**
     * @return void
     */
    public function authCodes()
    {
        $table = $this->loadModel('OAuthServer.AuthCodes');

        $this->process($table);
    }

    /**
     * @return void
     */
    public function refreshTokens()
    {
        $table = $this->loadModel('OAuthServer.RefreshTokens');

        $this->process($table);
    }

    /**
     * @param RepositoryInterface $table target table
     * @return void
     */
    private function process(RepositoryInterface $table)
    {
        if (!$table instanceof RevocableTokensTableInterface) {
            throw new InvalidArgumentException(__('$table should implement RevocableTokensTableInterface'));
        }

        $doExpired = $this->param('expired');
        $doRevoked = $this->param('revoked');

        if ($doExpired) {
            $table->find('Expired')->each(static function (EntityInterface $entity) use ($table) {
                $table->delete($entity);
            });
        }
        if ($doRevoked) {
            $table->find('Revoked')->each(static function (EntityInterface $entity) use ($table) {
                $table->delete($entity);
            });
        }
    }
}
