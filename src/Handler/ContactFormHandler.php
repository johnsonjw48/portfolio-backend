<?php

namespace App\Handler;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ContactFormHandler
{
    public function __construct(
        private FormFactoryInterface $formFactory
    ) {
    }

    public function handle(Request $request): array
    {
        $contact = new Contact();
        $form = $this->formFactory->create(ContactType::class, $contact);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            return [
                'success' => true,
                'contact' => $contact,
                'errors' => []
            ];
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return [
            'success' => false,
            'contact' => null,
            'errors' => $errors
        ];
    }
}
