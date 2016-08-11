# CDP_2.19.0

## CDP_2.19.0_RC1 (CDP_2.19.0_BETA9)

### Migrations

Needed to SSH into the server in order to run the migrations with a higher PHP memory limit. The migrations take quite some time to complete, since it migrates all of the AuditLog blob data.

Command run was:

```bash
php -d memory_limit=-1 app/console doctrine:migrations:migrate --em=default -e=prod
```

