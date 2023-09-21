## Implementation notes

This fork comes with 3 binaries of "shoutrrr" (arm64, armhf and x86). Depending on the system you use Pi.Alert on, you have to define in the configuration file in the parameter "SHOUTRRR_BINARY" which binary should be used.

[Shoutrrr Documentation - Telegram](https://containrrr.dev/shoutrrr/0.7/services/telegram/)

<hr>

Brief summary of the work steps

1. https://core.telegram.org/bots#how-do-i-create-a-bot or https://core.telegram.org/bots/features#creating-a-new-bot
2. Send a Message to your new created Bot
3. Change to the shoutrrr directory corresponding to your system (e.g. $HOME/pialert/back/shoutrrr/armhf)
4. run `./shoutrrr generate telegram`
5. enter the API token you got when you created the bot
```
Generating URL for telegram using telegram generator
To start we need your bot token. If you haven't created a bot yet, you can use this link:
  https://t.me/botfather?start

Enter your bot token: <YOUR API TOKEN>
Fetching bot info...

```

6. Fetching Chat ID
```
Okay! @<Bot_NAME> will listen for any messages in PMs and group chats it is invited to.
Waiting for messages to arrive...
Got Message '<The message you just sent to the bot>' from @ in private chat THE_CHAT_ID
Added new chat @!
``` 

7. Select Chat ID
```
Got 1 chat ID(s) so far. Want to add some more? no

Cleaning up the bot session...
Selected chats:
  THE_CHAT_ID (private) @
```
8 Final shoutrrr URL
```
telegram://<YOUR API TOKEN>@telegram?chats=<THE_CHAT_ID>&preview=No
```

9. You enter this URL into the pialert.conf file like:
```
TELEGRAM_BOT_TOKEN_URL  = 'telegram://<YOUR API TOKEN>@telegram?chats=<THE_CHAT_ID>&preview=No'
```

10. It should work

[Back](https://github.com/leiweibau/Pi.Alert#back)
