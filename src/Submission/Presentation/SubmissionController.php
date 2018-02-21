<?php declare(strict_types=1);

namespace SocialNews\Submission\Presentation;

use SocialNews\Submission\Presentation\SubmissionFormFactory;
use SocialNews\Framework\Csrf\Token;
use SocialNews\Submission\Application\SubmitLink;
use SocialNews\Submission\Application\SubmitLinkHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SocialNews\Framework\Rendering\TemplateRenderer;
use Symfony\Component\HttpFoundation\Session\Session;

final class SubmissionController
{
	private $templateRenderer;
	private $submissionFormFactory;
	private $session;
	private $submitLinkHandler;

	public function __construct(
		TemplateRenderer $templateRenderer,
		SubmissionFormFactory $submissionFormFactory,
		Session $session,
        SubmitLinkHandler $submitLinkHandler
	)
	{
		$this->templateRenderer = $templateRenderer;
		$this->submissionFormFactory = $submissionFormFactory;
		$this->session = $session;
		$this->submitLinkHandler = $submitLinkHandler;
	}

	public function show(): Response
	{
		$content = $this->templateRenderer->render('Submission.html.twig');
		return new Response($content);
	}

	public function submit(Request $request): Response
	{
		$response = new RedirectResponse('/submit');

		$form = $this->submissionFormFactory->createFromRequest($request);
		if ($form->hasValidationErrors()) {
		    foreach ($form->getValidationErrors() as $errorMessage) {
		        $this->session->getFlashBag()->add('errors', $errorMessage);
            }
            return $response;
        }

        $this->submitLinkHandler->handle($form->toCommand());
		$this->session->getFlashBag()->add(
			'success',
			'Your URL was submitted successfully'
		);

		return $response;
	}
}
