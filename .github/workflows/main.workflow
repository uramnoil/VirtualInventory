workflow "Lint Workflow" {
  resolves = [
    "lint",
  ]
  on = "commit_comment"
}

action "lint" {
  uses = "michaelw90/PHP-Lint@1.0.0"
}
