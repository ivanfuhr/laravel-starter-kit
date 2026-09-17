# Pull requests

Write the pull request title and body in Portuguese. Keep symbols, paths, class names, and commands in the original form.

Do not append "Made with Cursor" or any other agent/tool footer.

# PR CI loop

After opening or pushing a pull request:

1. Refresh live PR state (`gh pr view`, `gh pr checks`). Do not act on stale status.
2. Watch checks to completion (`gh pr checks --watch`) instead of polling in a tight loop.
3. If anything is red, read the failing log, fix only in-scope failures, push, and repeat from step 1.
4. Report success only after a fresh read shows every check green.

Do not merge the PR, enable auto-merge, or mark a draft ready. Leave those to the user.
