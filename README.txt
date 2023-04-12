# Telegram Reflection Prompter

Use this script to get reflection prompts via Telegram bot.

## How to set up

- `cp settings.php.example settings.php`
- Create bot with `BotFather` (= botid)
- Get your user ID with `userinfobot` (= chatid)
- Enter botid and chatid in `settings.php`
- Create a SQlite database with prompts (see schema below)
- Set up crontab to run `tg-reflect.php` periodically

## Database Schema

```sql
CREATE TABLE "prompts" ("id" integer,"body" text,"length" integer, "rating" integer, "vetted" integer, PRIMARY KEY (id));
```