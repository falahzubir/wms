<?php

namespace App\Handler;

use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\InvalidConfig;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class CustomSignatureValidator implements SignatureValidator
{
    // public function isValid(Request $request, WebhookConfig $config): bool {
    //     logger(hash_hmac('sha256', $request->getContent(), $config->signingSecret));
    //     return true;
    //   }

      public function isValid(Request $request, WebhookConfig $config): bool
    {

        $signature = $request->header($config->signatureHeaderName);

        if (! $signature) {
            return false;
        }

        $secret = $config->signingSecret;

        if (empty($secret)) {
            throw InvalidConfig::signingSecretNotSet();
        }


        $computedSignature = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($signature, $computedSignature);
    }
}
