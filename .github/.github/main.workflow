workflow "Lint Workflow" {
  on = "push"
  resolves = [
    "lint"
  ]
}

action "lint" {
  uses = "michaelw90/PHP-Lint@1.0.0"
}
