# Using Assetic in the Dev Environment

As shown in the README, to dump the web assets you can use the command:
```
php app/console assetic:dump
```

This physically writes all of the asset files you need for your dev environment. The big disadvantage is that you need to run this each time you update an asset. Fortunately, by passing the --watch option, the command will automatically regenerate assets as they change:
```
php app/console assetic:dump --watch
```

This will leave the process running and will regenerate the asset files any time they are modified. This is recommended while working with CSS and JS files in the project.

If you want to run this process in the background of the terminal, you can add `&` after the command:
```
php app/console assetic:dump --watch &
```

If you want to bring it back, you can simply type: `fg` and it will bring the process back to the foreground.

To send it back to the background, you can press `Ctrl + Z`.

To view the list of current jobs, enter: `jobs`

To kill a job, either bring it to the foreground and press `Ctrl + C`, or enter: `kill %1` where 1 is the job ID (shown when entering the `jobs` command).
