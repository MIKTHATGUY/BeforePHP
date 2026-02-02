<?php
declare(strict_types=1);

namespace NextPHP\Core;

class Page
{
	private ?string $controllerPath;
	private ?string $viewPath;
	private ?string $layoutPath;
	private array $params;
	private array $postData;
	private string $requestMethod;
	private bool $autoValidate;

	public function __construct(?string $controllerPath, ?string $viewPath, ?string $layoutPath = null, array $params = [], array $postData = [], string $requestMethod = 'GET', bool $autoValidate = true)
	{
		$this->controllerPath = $controllerPath;
		$this->viewPath = $viewPath;
		$this->layoutPath = $layoutPath;
		$this->params = $params;
		$this->postData = $postData;
		$this->requestMethod = $requestMethod;
		$this->autoValidate = $autoValidate;
	}

	public function render(): void
	{
		$page = '';
		
		// Auto-validate if enabled and schema is set
		if ($this->autoValidate && !empty(Validator::getErrors())) {
			// Validation already failed in preRender
			throw new ValidationException(Validator::getErrors());
		}
		
		// Make params available as _variables in controller/view context (e.g., $_slug, $_id)
		foreach ($this->params as $key => $value) {
			$varName = '_' . $key;
			$$varName = $value;
		}
		
		// Make POST data available as _variables (for form submissions, e.g., $_name, $_email)
		foreach ($this->postData as $key => $value) {
			$varName = '_' . $key;
			$$varName = $value;
		}
		
		// Make request method available
		$_method = $this->requestMethod;
		$_isPost = $this->requestMethod === 'POST';
		
		// Auto-validation helper functions available in page
		$_validator = Validator::class;
		$_validationErrors = Validator::getErrors();
		$_hasValidationErrors = Validator::hasErrors();
		
		if ($this->viewPath !== null) {
			// Has separate view: run controller first (if exists), then capture view
			if ($this->controllerPath !== null) {
				require $this->controllerPath;
			}
			ob_start();
			require $this->viewPath;
			$page = ob_get_clean();
		} elseif ($this->controllerPath !== null) {
			// No view, controller is the full page
			ob_start();
			require $this->controllerPath;
			$page = ob_get_clean();
		}
		
		// Render layout with $page variable (or just output page if no layout)
		if ($this->layoutPath !== null) {
			require $this->layoutPath;
		} else {
			echo $page;
		}
	}

	public function getParam(string $key, $default = null)
	{
		return $this->params[$key] ?? $default;
	}

	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * Get controller path
	 */
	public function getControllerPath(): ?string
	{
		return $this->controllerPath;
	}

	/**
	 * Get view path
	 */
	public function getViewPath(): ?string
	{
		return $this->viewPath;
	}

	/**
	 * Get layout path
	 */
	public function getLayoutPath(): ?string
	{
		return $this->layoutPath;
	}

	/**
	 * Enable or disable auto-validation
	 */
	public function setAutoValidate(bool $enabled): void
	{
		$this->autoValidate = $enabled;
	}

	/**
	 * Check if auto-validation is enabled
	 */
	public function isAutoValidate(): bool
	{
		return $this->autoValidate;
	}

	/**
	 * Pre-render validation (called by App before rendering)
	 */
	public function preValidate(): void
	{
		if (!$this->autoValidate) {
			return;
		}

		try {
			// Merge all input data for validation
			$allData = array_merge($this->params, $this->postData);
			Validator::validate($allData);
		} catch (ValidationException $e) {
			// Validation errors are stored, will be thrown in render()
			// or can be accessed via $_validationErrors
		}
	}
}
