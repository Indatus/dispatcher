# Dispatcher Contribution Guide

Thank you for considering contributing to Dispatcher. Please read the documentation below to determine where and how you should make contributions.

## Contribution Guide Contents

* [Which Branch](#which-branch)
* [Pull Requests](#pull-requests)
  * [Feature Requests](#feature-requests)

<a name="which-branch" />
## Which Branch?

**ALL** bug fixes should be made to the versioned branch to which they belong or `develop`. Bug fixes should never be sent to the `master` branch unless they fix features that exist only in the upcoming release.  The `master` branch will contain features for the next release.  Each versioned release will have a tag with the associated version number.

<a name="pull-requests" />
## Pull Requests

The pull request process differs for new features and bugs. Before sending a pull request for a new feature, you should first create an issue with `[Proposal]` in the title. The proposal should describe the new feature, as well as implementation ideas. The proposal will then be reviewed and either approved or denied. Once a proposal is approved, a pull request may be created implementing the new feature. Pull requests which do not follow this guideline will be closed immediately.

Pull requests for bugs may be sent without creating any proposal issue. If you believe that you know of a solution for a bug that has been filed on Github, please leave a comment detailing your proposed fix.

<a name="feature-requests" />
### Feature Requests

If you have an idea for a new feature you would like to see added to Dispatcher, you may create an issue on Github with `[Request]` in the title. The feature request will then be reviewed by a core contributor.
