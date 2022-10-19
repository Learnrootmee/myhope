<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

final class AdminNavigation
{
    const PARENT_SLUG = 'staatic';

    /**
     * @var mixed[]
     */
    private $menuItems = [];

    /**
     * @var mixed[]
     */
    private $pages = [];

    /**
     * @param int|null $position
     * @return void
     */
    public function addMenuItem(
        string $menuTitle,
        string $pageTitle,
        string $pageSlug,
        $pageRenderCallback,
        string $capability,
        $lazyLoadCallback = null,
        $position = null
    )
    {
        $this->menuItems[$pageSlug] = [
            'menuTitle' => $menuTitle,
            'pageTitle' => $pageTitle,
            'callback' => $pageRenderCallback,
            'capability' => $capability,
            'lazyLoadCallback' => $lazyLoadCallback,
            'position' => $position
        ];
    }

    /**
     * @param string|null $appearAsPageSlug
     * @return void
     */
    public function addPage(
        string $pageTitle,
        string $pageSlug,
        $pageRenderCallback,
        string $capability,
        $appearAsPageSlug = null,
        $lazyLoadCallback = null
    )
    {
        $this->pages[$pageSlug] = [
            'pageTitle' => $pageTitle,
            'callback' => $pageRenderCallback,
            'capability' => $capability,
            'appearAs' => $appearAsPageSlug ?: self::PARENT_SLUG,
            'lazyLoadCallback' => $lazyLoadCallback
        ];
    }

    /**
     * @return void
     */
    public function registerHooks()
    {
        \add_action('admin_menu', [$this, 'adminMenuSetup']);
        \add_action('submenu_file', [$this, 'hidePagesFromMenu']);
    }

    /**
     * @return void
     */
    public function adminMenuSetup()
    {
        \add_menu_page(
            \__('Staatic', 'staatic'),
            \__('Staatic', 'staatic'),
            'edit_posts',
            self::PARENT_SLUG,
            '',
            'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAyOTIuMTUgMjkyLjE1IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxnIGZpbGw9IiNmMWYyZjMiPjxnIHN0cm9rZS13aWR0aD0iMS4zMDEiPjxwYXRoIGQ9Ik0xNjkuNTMgMjA4LjEybDEuNzQ0LjE3ek0xMTAuMzggMTYzLjkzYzEzLjI0OCAxOC42ODggMTguNDQxIDI0LjA4OSA0My4xMDMgMjQuMDg5IDQwLjM0NCAwIDg0LjM0NS0xNi45MTggODkuNzItNjYuMzA3IDEuMDgtOS45My0yLjAwNC0xOS41MjEtNS42MzUtMjguMTg5YTczLjExNCA3My4xMTQgMCAxMC03Ni4zOCAxMTMuMjIgNzMuMjk2IDczLjI5NiAwIDAxLTUwLjgwNy00Mi44MTd6TTE2My45OSAyMDcuMjhxMi4yMzguNDE2IDQuNTI5LjcwMy0yLjI3OC0uMjg3LTQuNTI5LS43MDN6TTE3NC40NSAyMDguNDlsLTEuNDU4LS4wNzh6TTE2MS4zMiAyMDYuNzRjLjg1OS4xOTUgMS43MTguMzkgMi42MDMuNTQ3LS44ODUtLjE4My0xLjc0NC0uMzY1LTIuNjAzLS41NDd6Ii8+PGNpcmNsZSBjeD0iMTk3LjU2IiBjeT0iMTAzLjMzIiByPSI0My40NDEiLz48cGF0aCBkPSJNMjEzLjQzIDQyLjc1N2E0My4zNSA0My4zNSAwIDAwLTMyLjg0OCAxNS4wMThoLjE1NmExLjkgMS45IDAgMDEuNTczLS4wNzhsLS41NzMuMDc4YTEuMzAxIDEuMzAxIDAgMDAtLjIzNC4xMDRsLS44MDcuOTYzYTIuNzMzIDIuNzMzIDAgMDAtLjEwNC41NmMxOC44MDYtMy40MSAzNy42MS0xLjcwNSA0OS41NyAxMS45NzMgOS4yNTQgMTAuNDEgMTUuODUyIDI0LjI5OCAyMS40MzUgMzcuMzEyYTQ0LjA0IDQ0LjA0IDAgMDA2LjI3My0yMi40NzYgNDMuNDQxIDQzLjQ0MSAwIDAwLTQzLjQ0MS00My40NTR6Ii8+PC9nPjxwYXRoIGQ9Ik0yMC41NzEgNjYuNDY4bDkuNzM1LTYuMDkgNDEuOTU3IDIyLjU3OC00LjA0NyA4LjMwM3oiLz48ZyBzdHJva2Utd2lkdGg9IjEuMzAxIj48cGF0aCBkPSJNMS4zNSA1NS43NzFzOS41MTQgNS40MjcgMjMuODA0IDEzLjIyMmw4LjkwMSA0LjU5NCA0LjQtNS4zNzVhMS45NTIgMS45NTIgMCAwMTIuNzMyLS4yNiAxLjkyNiAxLjkyNiAwIDAxLjI2IDIuNzJsLTMuOTA0IDQuNzM3IDMuOTA0IDEuOTc4IDMuMzk3LTQuMTM4YTEuOTQgMS45NCAwIDExMi45OTMgMi40NmwtMi45MTUgMy40ODcgMy4zMDYgMS43MDUgNC43MS01Ljc0YTEuOTQgMS45NCAwIDAxMi45OTQgMi40NmwtNC4xOSA1LjEwMiAzLjQxIDEuNzU3IDIuNjAyLTMuMTc1YTEuOTQgMS45NCAwIDExMi45OTMgMi40NDZsLTIuMDk1IDIuNjAzIDMuNzM1IDEuOTUyIDQuNDc3LTUuNDUzYTEuOTQgMS45NCAwIDExMi45OTMgMi40NDdsLTMuOTA0IDQuODI4IDIyLjUxNSAxMS43MTNjMjUuODA3IDEyLjQxNiA0OS45NzUgMjIuNDkgNjAuNTAzIDIyLjQ5IDI3LjQyMSAwIDQ5LjY2Mi0xNS4zMTkgNDkuNjYyLTM0LjIyOCAwLTE4LjkxLTIyLjI5My0zNC4zMy00OS43MTQtMzQuMzN6TTI3LjM4IDY2LjQ4MmExLjMwMSAxLjMwMSAwIDAxMC0uMzc3IDEuOTEzIDEuOTEzIDAgMDEuMTA0LS4zNjUgMS4zMDEgMS4zMDEgMCAwMS4xODItLjMyNSAxLjgzNSAxLjgzNSAwIDAxLjIzNC0uMyAxLjk1MiAxLjk1MiAwIDAxMS43NTctLjUzM2wuMzUxLjExN2ExLjM2NyAxLjM2NyAwIDAxLjMzOS4xNyAyLjc0NiAyLjc0NiAwIDAxLjMuMjQ2IDEuODM1IDEuODM1IDAgMDEuMjMzLjMgMS4zMDEgMS4zMDEgMCAwMS4xODMuMzI1IDEuMzAxIDEuMzAxIDAgMDEuMTA0LjM2NSAxLjg0OCAxLjg0OCAwIDAxMCAuNzU0IDEuMzAxIDEuMzAxIDAgMDEtLjEwNC4zNjUgMS4zMDEgMS4zMDEgMCAwMS0uMTgzLjMyNSAxLjgzNSAxLjgzNSAwIDAxLS4yMzQuMyAxLjY5MiAxLjY5MiAwIDAxLS4zLjIzNCAxLjA2NyAxLjA2NyAwIDAxLS4zMzguMTgybC0uMzUuMTE3aC0uMzc4YTEuOTUyIDEuOTUyIDAgMDEtMS4zOC0uNTczIDEuODM1IDEuODM1IDAgMDEtLjIzNC0uMjk5IDEuMzAxIDEuMzAxIDAgMDEtLjE4Mi0uMzI1IDEuOTEzIDEuOTEzIDAgMDEtLjEwNC0uMzY1IDEuMzAxIDEuMzAxIDAgMDEuMDM5LS4zNTF6Ii8+PHBhdGggZD0iTTIyMi45NCA1NS44ODhhMzguMTMyIDM4LjEzMiAwIDAwLTQ0LjU2MSA0LjY4NSAzMi44NzQgMzIuODc0IDAgMDEzLjA1OCAzLjQyM2MxLjIzNyAwIDIuNDg2LS4wOTEgMy43MjItLjA5MWE3Ni45OTIgNzYuOTkyIDAgMDE1Ni44NDYgMjQuOTc0IDM4LjEzMiAzOC4xMzIgMCAwMC0xOS4wNjYtMzIuOTkxek0yNDIuMDEgODguODc5YzIuOTU0IDIyLjQ0OSAxLjEwNiAyOS4wMzUgMS4xMDYgMjkuMDM1bDI4LjcyMiA1My4yMTVhMzIuMDggMzIuMDggMCAwMDMuMzMyLTUuODk1eiIvPjxwYXRoIGQ9Ik0yNzUuMTcgMTY1LjIzYTMyLjA4IDMyLjA4IDAgMDEtMy4zMzIgNS44OTVsMTguOTYyIDMzLjY4MXoiLz48Y2lyY2xlIGN4PSIyNDIuMDEiIGN5PSI4OS40NTIiIHI9IjQuMTY1Ii8+PHBhdGggZD0iTTE1Ny40OSAxMjIuOTZhMTUuMDgzIDE1LjA4MyAwIDAxLTIuNDYtLjIzNGMtNS42NDgtLjkzNy0xNC43OTctNC42ODUtMjcuMTg3LTExLjE0Qzk2LjE4IDk1LjExIDUxLjkxOCA2NS40NSAzOS40OSA1Ny4wNjloMTE4YzExLjgwNCAwIDIyLjkzMSA0LjMwNyAzMC41MTggMTEuODA0YTMzLjIxMiAzMy4yMTIgMCAwMTIuOTQyIDMuMzA1IDI4Ljk1NyAyOC45NTcgMCAwMTYuMzUgMTcuMDIzdjEuNjkyYy0uNTcyIDE3LjY2LTE4LjM4OSAzMi4wNjctMzkuODEgMzIuMDY3eiIvPjxwYXRoIGQ9Ik0xNTcuNDkgNTguMjdjMTEuNDc5IDAgMjIuMjggNC4xNjUgMjkuNjMzIDExLjQzOWEyOS43NjMgMjkuNzYzIDAgMDEyLjgzNyAzLjE4OCAyNy42OTQgMjcuNjk0IDAgMDE2LjA5IDE2LjMzNHYxLjU2MWMtLjUyIDE3LjA1LTE3LjgxNSAzMC45MjItMzguNTYgMzAuOTIyYTEzLjkyNSAxMy45MjUgMCAwMS0yLjI1Mi0uMjIxYy01LjUxOC0uOTExLTE0LjQ3Mi00LjU1NS0yNi44MjItMTEuMDYyLTI5LjE5LTE1LjE4OC02OS4xMTgtNDEuNjQ1LTg0LjgyNy01Mi4yMTNoMTEzLjltMC0yLjUxMkgzNS40MnM1NC4zNiAzNy40NDIgOTEuODQxIDU2Ljk1YzExLjQyNiA1Ljk0OCAyMS4yOTEgMTAuMjE2IDI3LjU2NCAxMS4yN2ExNy4yMzEgMTcuMjMxIDAgMDAyLjYwMy4yNDhjMjIuMzg0LjA1MiA0MC42MDQtMTQuODEgNDEuMTM4LTMzLjI2NHYtMS43N2EzMC4xNTQgMzAuMTU0IDAgMDAtNi42MjQtMTcuNzUxIDMyLjgzNSAzMi44MzUgMCAwMC0zLjAyLTMuNDc1Yy03LjU3NC03LjQ0NC0xOC44MzEtMTIuMTQyLTMxLjQyOC0xMi4xNDJ6Ii8+PC9nPjxwYXRoIGQ9Ik0xNjYuOTI3IDIxOC4yNWw2Ljk3NS01LjEyOCAyNi4wMjggMTcuNzI1LTUuOTA4IDYuNzI5eiIvPjxnIHN0cm9rZS13aWR0aD0iMS4zMDEiPjxwYXRoIGQ9Ik0xMTQuMzkgMTczLjE5YzMuNzg3IDcuMzkyIDE5LjUyMSAyMC43MzIgMzcuNTQ2IDM0LjM3MWwyMy44MDMgMTYuOTE4IDQuMzM0LTQuMzM0YTEuOTQgMS45NCAwIDExMi43NDYgMi43MzNsLTMuOTA1IDMuOTA1IDMuMDk4IDIuMTk5IDIuMjUxLTIuMjUxYTEuOTQyIDEuOTQyIDAgMTEyLjc0NiAyLjc0NmwtMS43OTYgMS43OTYgMy4zODQgMi40MDcgNC41ODEtNC41OTRhMS45NDIgMS45NDIgMCAwMTIuNzQ2IDIuNzQ2bC00LjEzOCA0LjEyNiA1Ljg4MiA0LjE5YzEwLjYwNyA3LjIzNiAxNy44MjkgMTEuOTYgMTcuODI5IDExLjk2bC01My4wNi0xMDMuNTRjLTkuODY0LTE5LjI0OC0yOC42My0yOS4zNDctNDEuODkyLTIyLjU1NC0xMy4yNjEgNi43OTQtMTYuMDIgMjcuOTI4LTYuMTU2IDQ3LjE3NnptNTcuNTc1IDQ1LjU1YTEuNzMgMS43MyAwIDAxMC0uMzY0IDEuNzA1IDEuNzA1IDAgMDEuMTE3LS4zNjUgMS42NCAxLjY0IDAgMDEuMTctLjMzOCAxLjc4MyAxLjc4MyAwIDAxLjI0Ny0uMjg2bC4yODYtLjI0OGEyLjE0NyAyLjE0NyAwIDAxLjMzOC0uMTgyIDEuMTcxIDEuMTcxIDAgMDEuMzY1LS4xMDQgMS44NDggMS44NDggMCAwMS43NTQgMCAxLjE3MSAxLjE3MSAwIDAxLjM2NS4xMDQgMi4xNDcgMi4xNDcgMCAwMS4zMzguMTgybC4yODcuMjQ4YTEuODg3IDEuODg3IDAgMDEuNTcyIDEuMyAxLjk1MiAxLjk1MiAwIDAxLS41NzIgMS4zOCAxLjk1MiAxLjk1MiAwIDAxLTIuNzMzIDAgMS44ODcgMS44ODcgMCAwMS0uMjQ4LS4yOTkgMS41NDkgMS41NDkgMCAwMS0uMTY5LS4zMjUgMS45NzggMS45NzggMCAwMS0uMTE3LS4zNjUgMS44ODcgMS44ODcgMCAwMS4wMzktLjM5eiIvPjxwYXRoIGQ9Ik0yMDAuMjkgMjI1LjE4Yy0xMS4zMDktNi4yMDgtNDYuODUxLTI2LjAyOC02OS4zMDEtNDEuOTcxLTguOTY3LTYuMzUtMTQuODYyLTExLjQtMTcuNTQzLTE1LjAxOGExMC44NDEgMTAuODQxIDAgMDEtMS4wMjgtMS42Yy03LjY0LTE0LjkwMi0zLjk5Ni0zMi41MzcgOC4xMzQtMzkuMTQ4bC41NzItLjMxMi41MjEtLjI2YTIxLjkyOSAyMS45MjkgMCAwMTkuNS0yLjEyMiAyNC43MjcgMjQuNzI3IDAgMDE0LjY0Ni40NDMgMjUuMjg3IDI1LjI4NyAwIDAxMy4zNDUuODcyIDM0LjA3MSAzNC4wNzEgMCAwMTE5LjEwNSAxNy4wNDl6Ii8+PHBhdGggZD0iTTEzMS4xNCAxMjYuMDVhMjQuNDE1IDI0LjQxNSAwIDAxNy42IDEuMzAxIDMyLjc5NiAzMi43OTYgMCAwMTE4LjM3NiAxNi40MjRsMy4wODUgNi4wMjYgMzcuMDkgNzIuMzg1Yy0xMy45NTEtNy43My00NS4yMjQtMjUuNDQzLTY1LjU4LTM5LjkwMi04Ljg2Mi02LjI4Ni0xNC42NjYtMTEuMjQ0LTE3LjI3LTE0Ljc0NWExMC4xOSAxMC4xOSAwIDAxLS45MS0xLjQzMmMtNy4zMjctMTQuMzE2LTMuOTA1LTMxLjExNyA3LjYxMy0zNy40NjhsLjU0Ny0uMjk5LjU2LS4yNzNhMjAuNDU4IDIwLjQ1OCAwIDAxOC44ODgtMi4wMTdtMC0yLjUxMmEyMy4wMDkgMjMuMDA5IDAgMDAtOS45NjkgMi4yMjVsLS42MjUuMy0uNjExLjMyNWMtMTIuNzggNy4wNTQtMTYuNjcxIDI1LjE0My04LjY0MiA0MC44MjZhMTMuNzU2IDEzLjc1NiAwIDAwMS4xMzIgMS43ODNjMi45OTQgNC4wMjEgOS41NCA5LjQxIDE3LjgzIDE1LjI5MiAyNy4xNzMgMTkuMyA3My4wMSA0My45ODggNzMuMDEgNDMuOTg4bC00MC44MjctNzkuNzEyLTMuMDg0LTYuMDI2YTM1LjIwMyAzNS4yMDMgMCAwMC0xOS44Mi0xNy42NiAyNC45MzUgMjQuOTM1IDAgMDAtMy41MTUtLjkxIDI1Ljg0NiAyNS44NDYgMCAwMC00Ljg4LS41MjF6TTc0Ljk1OSAxMzYuODVhMS4xMiAxLjEyIDAgMDAuNzY4LS4zMTJsMzAuODA1LTMwLjgwNWExLjEwNiAxLjEwNiAwIDAwMC0xLjUzNiAxLjA4IDEuMDggMCAwMC0xLjUzNiAwbC0zMC44MDUgMzAuODA1YTEuMDggMS4wOCAwIDAwLjc2OCAxLjg0OHpNNzQuNzc3IDE1Mi42YTEuMDY3IDEuMDY3IDAgMDAuNzY4LS4zMTJsOS41MjYtOS41MjdhMS4wOSAxLjA5IDAgMTAtMS41NjEtMS41MjJsLTkuNDg4IDkuNTM5YTEuMDggMS4wOCAwIDAwMCAxLjUzNiAxLjA2NyAxLjA2NyAwIDAwLjc1NS4yODZ6TTg4LjgwNiAxMzguNThhMS4xMiAxLjEyIDAgMDAuNzY4LS4zMTJsMTIuMTAzLTEyLjExNmExLjA4IDEuMDggMCAwMDAtMS41MzYgMS4wNjcgMS4wNjcgMCAwMC0xLjUyMyAwbC0xMi4xMTYgMTIuMTE2YTEuMDggMS4wOCAwIDAwLjc2OCAxLjg0OHpNNjkuMDc2IDE3Mi45YTEuMTIgMS4xMiAwIDAwLjc2OC0uMzEybDMwLjQ1My0zMC40NjZhMS4wOCAxLjA4IDAgMDAwLTEuNTM2IDEuMTA2IDEuMTA2IDAgMDAtMS41MzYgMGwtMzAuNDUzIDMwLjQ2NmExLjA4IDEuMDggMCAwMC43NjggMS44NDh6TTgzLjQzMSAxNzQuMDZhMS4wNjcgMS4wNjcgMCAwMC43NjgtLjMxMmw4LjY1NC04LjY1NWExLjA4IDEuMDggMCAwMDAtMS41MzUgMS4xMDYgMS4xMDYgMCAwMC0xLjUzNSAwbC04LjY1NSA4LjY1NGExLjEwNiAxLjEwNiAwIDAwMCAxLjUzNiAxLjEyIDEuMTIgMCAwMC43NjguMzEyeiIvPjxwYXRoIGQ9Ik05Ny44MjUgMTU5LjY5YTEuMDkzIDEuMDkzIDAgMDAuNzY4LS4zMTJsMjUuMDEzLTI1LjAxM2ExLjA4MSAxLjA4MSAwIDAwLTEuNTM2LTEuNTIzbC0yNS4wMTMgMjVhMS4xMDYgMS4xMDYgMCAwMDAgMS41MzYgMS4xMiAxLjEyIDAgMDAuNzY4LjMxMnpNNzUuMjk3IDE5Ny4wOGExLjAyOCAxLjAyOCAwIDAwLjc2OC0uMzI1bDQ4LjE1My00OC4xNTNhMS4wODYgMS4wODYgMCAwMC0xLjUzNi0xLjUzNkw3NC41MyAxOTUuMjE5YTEuMDkzIDEuMDkzIDAgMDAuNzY4IDEuODQ4ek05OS44ODEgMTg4LjA3YTEuMTIgMS4xMiAwIDAwLjc2OC0uMzEybDIxLjQ2LTIxLjQ2YTEuMDg2IDEuMDg2IDAgMDAtMS41MzYtMS41MzZsLTIxLjQ0NyAyMS40NmExLjA4IDEuMDggMCAwMC43NjggMS44NDh6TTg2LjM3MiAyMTYuMTJhMS4wMjggMS4wMjggMCAwMC43NjgtLjMyNWw5LjExLTkuMTc1YTEuMDY3IDEuMDY3IDAgMDAwLTEuNTIzIDEuMDggMS4wOCAwIDAwLTEuNTM2IDBsLTkuMTEgOS4xMWExLjA4IDEuMDggMCAwMDAgMS41MzYgMS4wMjggMS4wMjggMCAwMC43NjguMzc3ek0xMDEuMjYgMjAxLjIzYTEuMDkzIDEuMDkzIDAgMDAuNzY4LS4zMTJsMjEuMjktMjEuMjkxYTEuMDg2IDEuMDg2IDAgMTAtMS41MzUtMS41MzZsLTIxLjI5IDIxLjI5MWExLjA4IDEuMDggMCAwMC43NjcgMS44NDh6TTEwOC44NyAyMDguODRhMS4wNjcgMS4wNjcgMCAwMC43NjgtLjMxMmwxMS4wNzUtMTEuMDc1YTEuMDg2IDEuMDg2IDAgMDAtMS41MzYtMS41MzZsLTExLjA3NSAxMS4wNzVhMS4xMDYgMS4xMDYgMCAwMDAgMS41MzYgMS4xMiAxLjEyIDAgMDAuNzY4LjMxMnpNMTA2LjAyIDIyNi41OGExLjEyIDEuMTIgMCAwMC43NjgtLjMxMmwyNC44Ny0yNC44NDRhMS4wNjcgMS4wNjcgMCAwMDAtMS41MjMgMS4wOCAxLjA4IDAgMDAtMS41MzYgMGwtMjQuODMgMjQuODMxYTEuMDggMS4wOCAwIDAwLjc2NyAxLjg0OHoiLz48L2c+PC9nPjwvc3ZnPg==',
            null
        );
        // Do the sorting here instead of passing it to WordPress.
        \uasort($this->menuItems, function ($a, $b) {
            return ($a['position'] ?? 100) <=> ($b['position'] ?? 100);
        });
        foreach ($this->menuItems as $pageSlug => $menuItem) {
            $hook = \add_submenu_page(
                self::PARENT_SLUG,
                $menuItem['pageTitle'],
                $menuItem['menuTitle'],
                $menuItem['capability'],
                $pageSlug,
                $menuItem['callback']
            );
            if ($menuItem['lazyLoadCallback']) {
                \add_action('load-' . $hook, $menuItem['lazyLoadCallback']);
            }
        }
        foreach ($this->pages as $pageSlug => $page) {
            $hook = \add_submenu_page(
                self::PARENT_SLUG,
                $page['pageTitle'],
                '',
                $page['capability'],
                $pageSlug,
                $page['callback']
            );
            if ($page['lazyLoadCallback']) {
                \add_action('load-' . $hook, $page['lazyLoadCallback']);
            }
        }
    }

    /**
     * @param string|null $submenuFile
     * @return string|null
     */
    public function hidePagesFromMenu($submenuFile)
    {
        global $plugin_page;
        // Submenu slug with alternative item slug to highlight.
        $mapping = \array_map(function ($page) {
            return $page['appearAs'];
        }, $this->pages);
        // Always remove the automatically generated submenu item for the parent slug
        $mapping[self::PARENT_SLUG] = null;
        // Select another submenu item to highlight.
        if ($plugin_page && isset($mapping[$plugin_page])) {
            $submenuFile = $mapping[$plugin_page];
        }
        // Hide the submenu.
        foreach (\array_keys($mapping) as $submenu) {
            \remove_submenu_page(self::PARENT_SLUG, $submenu);
        }

        return $submenuFile;
    }
}
