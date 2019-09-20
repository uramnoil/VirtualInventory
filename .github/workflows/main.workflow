workflow "Lint Workflow" {
  resolves = ["build"]
  on = "push"
}

action "build" {
  uses = uramnoil/pmmp-plugin-build-action
}
