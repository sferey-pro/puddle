<?php

final class SagaContext implements Context
{

    /**
     * @Given la création du profil utilisateur pour ":email" est configurée pour échouer
     */
    public function userProfileCreationIsConfiguredToFail(string $email): void
    {
        $this->failureSimulator->shouldFailFor($email);
    }

}
