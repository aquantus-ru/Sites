# Winter CMS - MSN 2007 Theme

This project contains a Winter CMS installation configured with a custom "MSN 2007" style theme and several plugins (Forum, Sitemap, Ratings).

## Installation

1.  **Clone the repository:**
    ```bash
    git clone <repository_url>
    cd <repository_folder>
    ```

2.  **Install Dependencies:**
    This step is crucial to download the Winter CMS core modules and the plugins (Forum, Sitemap).
    ```bash
    composer install
    ```

3.  **Setup Environment:**
    Copy the example environment file and configure your database settings (if not using SQLite).
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Note: The project comes with a pre-configured SQLite setup in `.env` for convenience.*

4.  **Migrate Database:**
    Run the migrations to set up the database tables for the CMS and plugins.
    ```bash
    php artisan winter:up
    ```

5.  **Serve the Application:**
    Start the local development server.
    ```bash
    php artisan serve
    ```

6.  **Access the Site:**
    Open your browser and navigate to: [http://localhost:8000](http://localhost:8000)

## Features

*   **MSN 2007 Theme:** A custom theme located in `themes/msn2007` with orange and gray styling.
*   **Style Test Page:** [http://localhost:8000/style-test](http://localhost:8000/style-test) - Showcases HTML element styling.
*   **Forums:** [http://localhost:8000/forum](http://localhost:8000/forum) - Functional forum using `Winter.Forum`.
*   **Ratings:** Custom voting component implemented in `plugins/jules/ratings`.
*   **Sitemap:** [http://localhost:8000/sitemap.xml](http://localhost:8000/sitemap.xml) - Generated sitemap.

## Troubleshooting

*   **404 Not Found:**
    *   Ensure you have run `php artisan winter:up` to migrate the database.
    *   Ensure you are accessing the correct URL (e.g., `/style-test` or `/forum`).
    *   If components are missing, ensure `composer install` was run successfully.
