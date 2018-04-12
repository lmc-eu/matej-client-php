'use strict';

const commits = danger.github.commits;
const pr = danger.github.pr;

// Fail when there are fixup/squash commits in the PR
for (const commit of commits) {
    const commitMessage = commit.commit.message;
    if (commitMessage.startsWith('fixup!') || commitMessage.startsWith('squash!')) {
        fail('There are some fixup/squash commits that needs to be applied before merge.');
        break;
    }
}

// Warn if PR is too big
if (pr.additions > 500) {
    warn('This PR is way too big, consider splitting it up to smaller ones. Reviewers will be thankful 🙏.')
}
