<?php

namespace OAuthServer\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * OAuth 2.0 shell
 *
 * Helper
 *
 * bin/cake oauth generate_encryption_key
 */
class OauthShell extends Shell
{
    /**
     * @inheritdoc
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->setDescription('OAuth 2.0 server helper utility shell');
        $parser->addSubcommand('generateEncryptionKey', [
            'parser' => (new ConsoleOptionParser()),
            'help'   => 'Generates a suitable OAuth 2.0 server encryption key',
        ]);
        return $parser;
    }

    /**
     * Generates encryption key
     */
    public function generateEncryptionKey()
    {
        $this->out(base64_encode(random_bytes(32)));
    }
}