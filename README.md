# GitHub to DoBoard Integration

This project integrates GitHub with DoBoard to track GitHub project issues and automatically create DoBoard tasks.

Optionally, it can also send notifications to a Telegram chat.

## About DoBoard

DoBoard is a CleanTalk project management tool that helps teams organize their work and collaborate effectively. Learn more at [DoBoard](https://doboard.com/).

## Prerequisites

- PHP version 7.3 or higher
- Composer

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/CleanTalk/github-to-doboard.git
    cd github-to-doboard
    ```

2. Install dependencies:
    ```sh
    composer install
    ```

3. Copy the example configuration file and update it with your credentials:
    ```sh
    cp .config.example .config
    ```

4. Edit the `.config` file with your DoBoard and Telegram Bot API credentials.

## Usage

1. Place the `pub/eZgakitv_webHook.php` file in the root of your web server. 

    For example, if you are using Apache, you can place the file in the `/var/www/html` directory.

    If you are using Nginx, you can place the file in the `/var/www/html` directory.

    If you are using a different web server, you can place the file in the root directory of your server.

    **Please note, the file name should be unique to prevent unauthorized access. We recommend to rename the file to a unique name.**

2. Ensure your web server is configured to handle requests to this file.

3. Set up a webhook in your GitHub repository settings to point to the URL of the `eZgakitv_webHook.php` file.

4. Any opened issue in the GitHub repository will be automatically created as a task in DoBoard. 

5. Optionally, you can enable Telegram notifications by adding the `TG_BOT_API_KEY` and `TG_CHAT_ID` environment variables to the `.config` file.

## Configuration

The `.config` file should contain the following obligatory environment variables:

```ini
DOBOARD_USERNAME=""
DOBOARD_PASSWORD=""
DOBOARD_COMPANY_ID=0
DOBOARD_PROJECT_ID=0
DOBOARD_AUTHOR_ID=0
```
Optionally, you can add the following environment variables to enable Telegram notifications:
```ini
TG_BOT_API_KEY=""
TG_CHAT_ID=0
```
To enable debug mode, set the `DEBUG` environment variable to `true`, `false` otherwise:
```ini
DEBUG=true
```

## License

This project is licensed under the GNU License. See the [LICENSE](LICENSE) file for details.

## Contributing

1. Fork the repository.
2. Create a new branch (git checkout -b feature-branch).
3. Make your changes.
4. Commit your changes (git commit -am 'Add new feature').
5. Push to the branch (git push origin feature-branch).
6. Create a new Pull Request.

## Contact

If you have any questions, feel free to contact us at [plugins@cleantalk.org](mailto:plugins@cleantalk.org)
