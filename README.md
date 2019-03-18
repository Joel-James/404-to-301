# Duck Dev Plugin Boilerplate [![Build Status](https://api.travis-ci.com/Joel-James/plugin-boilerplate.svg?branch=master)](https://travis-ci.com/Joel-James/plugin-boilerplate)

Before starting development make sure you read and understand everything in this README.

Also, don't forget to document your code properly.

## Working with Git

Clone the plugin repo and checkout the `development` branch

```
# git clone https://github.com/Joel-James/plugin-boilerplate.git --recursive
# git fetch && git checkout development
```

Install/update the necessary submodules if the branch is already checked out

```
# git submodule init --
# git submodule update  
```

## Installing dependencies and initial configuration

Install Node
```
# curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -
# sudo apt-get install -y nodejs build-essential
```

Install the necessary npm modules and packages
```
# npm install
``` 

After that for the first time, run below command to create assets.
```
# npm run compile
``` 

Set up username and email for Git commits
```
# git config user.email "<your email>"
# git config user.name "<your name>"
```

## Build tasks (npm)

Everything (except unit tests) should be handled by npm. Note that you don't need to interact with Grunt in a direct way.

Command | Action
------- | ------
`npm run translate` | Build pot and mo file inside /languages/ folder
`npm run compile` | Compile assets
`npm run build` | Build release version, useful to provide packages to QA without doing all the release tasks

## Versioning

Follow semantic versioning [http://semver.org/](http://semver.org/) as `package.json` won't work otherwise. That's it:

- `X.X.0` for mayor versions
- `X.X.X` for minor versions
- `X.X[.X||.0]-rc.1` for release candidates
- `X.X[.X||.0]-beta.1` for betas (QA builds)
- `X.X[.X||.0]-alpha.1` for alphas (design check tasks)

## Workflow

Do not commit on `master` branch (should always be synced with the latest released version). `development` is the code
that accumulates all the code for the next version.

- Create a new branch from `development` branch: `git checkout -b branch-name origin/development`. Try to give it a descriptive name. For example:
    * `release/X.X.X` for next releases
    * `new/some-feature` for new features
    * `enhance/some-enhancement` for enhancements
    * `fix/some-bug` for bug fixing
- Make your commits and push the new branch: `git push -u origin branch-name`
- File the new Pull Request against `development` branch
- Assign somebody to review your code.
- Once the PR is approved and finished, merge it in `development` branch.
- Delete your branch locally and make sure that it does not longer exist remote.

It's a good idea to create the Pull Request as soon as possible so everybody knows what's going on with the project
from the PRs screen in Bitbucket.

## How to release?

Prior to release, code needs to be checked and tested by QA team. Merge all active Pull Requests into `development` branch. Build the release with `npm run build` script and send the zip files to QA.

Follow these steps to make the release:

* Update `changelog.text` file.
* Once you have your `development` branch ready, merge into `master`. Do not forget to update the version number. Always with
format X.X.X. You'll need to update in `pro-sites.php` (header and $version variable) and also `package.json`
* Execute `npm run build`. zips and files will be generated in `releases` folder.
* Do not forget to sync `master` on `development` by checking out `development` branch and then `git merge master`