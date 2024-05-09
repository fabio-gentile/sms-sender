## Ajouter dans .env

```
DEEPL_API=YOUR_API_KEY
TWILIO_DSN=YOUR_DSN
```

### Lancer le messenger + scheduler
-vv pour afficher les logs.
```
php bin/console messenger:consume async -vv scheduler_sms
```