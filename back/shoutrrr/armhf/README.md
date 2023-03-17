<div align="center">

<a href="https://github.com/containrrr/shoutrrr">
    <img src="https://raw.githubusercontent.com/containrrr/shoutrrr/main/docs/shoutrrr-logotype.png" width="450" />
</a>

# Shoutrrr

Notification library for gophers and their furry friends.
Heavily inspired by <a href="https://github.com/caronc/apprise">caronc/apprise</a>.

![github actions workflow status](https://github.com/containrrr/shoutrrr/workflows/Main%20Workflow/badge.svg)
[![codecov](https://codecov.io/gh/containrrr/shoutrrr/branch/main/graph/badge.svg)](https://codecov.io/gh/containrrr/shoutrrr)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/47eed72de79448e2a6e297d770355544)](https://www.codacy.com/gh/containrrr/shoutrrr/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=containrrr/shoutrrr&amp;utm_campaign=Badge_Grade)
[![report card](https://goreportcard.com/badge/github.com/containrrr/shoutrrr)](https://goreportcard.com/badge/github.com/containrrr/shoutrrr)
[![go.dev reference](https://img.shields.io/badge/go.dev-reference-007d9c?logo=go&logoColor=white&style=flat-square)](https://pkg.go.dev/github.com/containrrr/shoutrrr)
[![github code size in bytes](https://img.shields.io/github/languages/code-size/containrrr/shoutrrr.svg?style=flat-square)](https://github.com/containrrr/shoutrrr)
[![license](https://img.shields.io/github/license/containrrr/shoutrrr.svg?style=flat-square)](https://github.com/containrrr/shoutrrr/blob/main/LICENSE)
[![godoc](https://godoc.org/github.com/containrrr/shoutrrr?status.svg)](https://godoc.org/github.com/containrrr/shoutrrr) <!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-14-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->

</div>
<br/><br/>

## Installation

### Using the snap

```bash
$ sudo snap install shoutrrr
```

### Using the Go CLI

```bash
$ go install github.com/containrrr/shoutrrr@latest
```

### From Source

```bash
$ go build -o shoutrrr .
```

## Quick Start

### As a package

Using shoutrrr is easy! There is currently two ways of using it as a package.

#### Using the direct send command

```go
  url := "slack://token-a/token-b/token-c"
  err := shoutrrr.Send(url, "Hello world (or slack channel) !")

```

#### Using a sender

```go
  url := "slack://token-a/token-b/token-c"
  sender, err := shoutrrr.CreateSender(url)
  sender.Send("Hello world (or slack channel) !", map[string]string { /* ... */ })
```


#### Using a sender with multiple URLs
```go
  urls := []string {
    "slack://token-a/token-b/token-c"
    "discord://token@channel"
  }
  sender, err := shoutrrr.CreateSender(urls...)
  sender.Send("Hello world (or slack channel) !", map[string]string { /* ... */ })
```

### Through the CLI

Start by running the `build.sh` script.
You may then run send notifications using the shoutrrr executable:

```shell
$ shoutrrr send [OPTIONS] <URL> <Message [...]>
```

### From a GitHub Actions workflow

You can also use Shoutrrr from a GitHub Actions workflow.

See this example and the [action on GitHub
Marketplace](https://github.com/marketplace/actions/shoutrrr-action):

```yaml
name: Deploy
on:
  push:
    branches:
      - main

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - name: Some other steps needed for deploying
        run: ...
      - name: Shoutrrr
        uses: containrrr/shoutrrr-action@v1
        with:
          url: ${{ secrets.SHOUTRRR_URL }}
          title: Deployed ${{ github.sha }}
          message: See changes at ${{ github.event.compare }}.
```

## Documentation
For additional details, visit the [full documentation](https://containrrr.dev/shoutrrr). 

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://github.com/amirschnell"><img src="https://avatars3.githubusercontent.com/u/9380508?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Amir Schnell</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=amirschnell" title="Code">💻</a></td>
    <td align="center"><a href="https://piksel.se"><img src="https://avatars2.githubusercontent.com/u/807383?v=4?s=100" width="100px;" alt=""/><br /><sub><b>nils måsén</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=piksel" title="Code">💻</a> <a href="https://github.com/containrrr/shoutrrr/commits?author=piksel" title="Documentation">📖</a> <a href="#maintenance-piksel" title="Maintenance">🚧</a></td>
    <td align="center"><a href="https://github.com/lukapeschke"><img src="https://avatars1.githubusercontent.com/u/17085536?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Luka Peschke</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=lukapeschke" title="Code">💻</a> <a href="https://github.com/containrrr/shoutrrr/commits?author=lukapeschke" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/MrLuje"><img src="https://avatars0.githubusercontent.com/u/632075?v=4?s=100" width="100px;" alt=""/><br /><sub><b>MrLuje</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=MrLuje" title="Code">💻</a> <a href="https://github.com/containrrr/shoutrrr/commits?author=MrLuje" title="Documentation">📖</a></td>
    <td align="center"><a href="http://simme.dev"><img src="https://avatars0.githubusercontent.com/u/1596025?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Simon Aronsson</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=simskij" title="Code">💻</a> <a href="https://github.com/containrrr/shoutrrr/commits?author=simskij" title="Documentation">📖</a> <a href="#maintenance-simskij" title="Maintenance">🚧</a></td>
    <td align="center"><a href="https://arnested.dk"><img src="https://avatars2.githubusercontent.com/u/190005?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Arne Jørgensen</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=arnested" title="Documentation">📖</a> <a href="https://github.com/containrrr/shoutrrr/commits?author=arnested" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/atighineanu"><img src="https://avatars1.githubusercontent.com/u/27206712?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Alexei Tighineanu</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=atighineanu" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/ellisab"><img src="https://avatars2.githubusercontent.com/u/1402047?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Alexandru Bonini</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=ellisab" title="Code">💻</a></td>
    <td align="center"><a href="https://senan.xyz"><img src="https://avatars0.githubusercontent.com/u/6832539?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Senan Kelly</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=sentriz" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/JonasPf"><img src="https://avatars.githubusercontent.com/u/2216775?v=4?s=100" width="100px;" alt=""/><br /><sub><b>JonasPf</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=JonasPf" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/claycooper"><img src="https://avatars.githubusercontent.com/u/3612906?v=4?s=100" width="100px;" alt=""/><br /><sub><b>claycooper</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=claycooper" title="Documentation">📖</a></td>
    <td align="center"><a href="http://ko-fi.com/disyer"><img src="https://avatars.githubusercontent.com/u/16326697?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Derzsi Dániel</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=darktohka" title="Code">💻</a></td>
    <td align="center"><a href="https://josephkav.io"><img src="https://avatars.githubusercontent.com/u/4267227?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Joseph Kavanagh</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/commits?author=JosephKav" title="Code">💻</a> <a href="https://github.com/containrrr/shoutrrr/issues?q=author%3AJosephKav" title="Bug reports">🐛</a></td>
    <td align="center"><a href="https://ring0.lol"><img src="https://avatars.githubusercontent.com/u/1893909?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Justin Steven</b></sub></a><br /><a href="https://github.com/containrrr/shoutrrr/issues?q=author%3Ajustinsteven" title="Bug reports">🐛</a></td>
  </tr>
</table>

<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->

<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!

## Related Project(s)
- [watchtower](https://github.com/containrrr/watchtower) - process for automating Docker container base image updates that uses shoutrrr for notifications
- [kured](https://github.com/weaveworks/kured) - kubernetes reboot daemon has adopted shoutrrr as their unified notification method starting with version 1.7.0.
