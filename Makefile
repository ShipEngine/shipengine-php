SHELL := /bin/bash
.DEFAULT_GOAL := help

.PHONY: help check-tag tag release full_release

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

check-tag: ## Ensures that the TAG variable was passed to the make command.
	$(if $(TAG),,$(error TAG is not defined. Pass via "TAG=X.Y.Z make tag - where X, Y, and Z are major, minor, and patch version numbers"))

# Creates a release but does not push it. This task updates the changelog with the TAG environment variable,
#replaces the VERSION constant in src/ShipEngine.php, and ensures that the source is still valid after updating,
# commits the changelog and updated VERSION constant, creates an annotated git tag using
# chag (https://github.com/mtdowling/chag), and prints out a diff of the last commit.
tag: check-tag ## Creates a packagist release but does not push it.
	TAG=$(TAG) bash ./scripts/bumpVersion.sh
	@echo "Tagging repository $(TAG) for release"
	chag update $(TAG)
	git commit -a -m '$(TAG) packagist release'
	chag tag
	@echo "A Packagist release has been created. Push using 'make release' to publish to Packagist."
	@echo "Changes made in the release commit:"
	git diff HEAD~1 HEAD

# Creates a release based on the master branch and latest tag. This task
# pushes the latest tag, pushes master. Use "TAG=X.Y.Z make tag" to create a release,
# and use "make release" to push a release.
release: check-tag ## Publish release to Packagist.
	git push origin master
	git push origin $(TAG)

full-release: tag release ## Tags the repo, pushes the tag, and publishes a release to Packagist.
