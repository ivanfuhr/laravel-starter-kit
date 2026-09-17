# Git commits

Do not attribute commits to AI coding agents or tools.

- Do not add `Co-authored-by`, `Signed-off-by`, or `Made-with` trailers for Cursor, Claude, Copilot, Codex, or any other agent.
- Do not set Author or Committer to an agent identity (for example `cursoragent@cursor.com`).
- Write only the project commit message.
- After `git commit`, read `git log -1 --format=%B`. If an agent trailer was injected, amend **only** to delete those lines — and only when the commit has not been pushed.
- Do not force-push to strip attribution from a commit that is already on the remote.
