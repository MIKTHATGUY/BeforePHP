<?php
declare(strict_types=1);

namespace NextPHP\Core;

class Page
{
	private ?string $controllerPath;
	private ?string $viewPath;
	private ?string $layoutPath;
	private array $params;

	public function __construct(?string $controllerPath, ?string $viewPath, ?string $layoutPath = null, array $params = [])
	{
		$this->controllerPath = $controllerPath;
		$this->viewPath = $viewPath;
		$this->layoutPath = $layoutPath;
		$this->params = $params;
	}

	public function render(): void
	{
		$page = '';
		
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
}