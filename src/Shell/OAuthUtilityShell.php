<?php

namespace OAuthServer\Lib;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * OAuth 2.0 utility shell
 */
class OAuthUtilityShell extends Shell
{
    /**
     * @inheritdoc
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
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
        return base64_encode(random_bytes(32));
    }
}
