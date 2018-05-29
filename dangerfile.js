'use strict';

const fs = require('fs');
const exec = require('child_process').exec;
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

// Print detected BC breaks
const bcChangesFile = 'bc-changes.md';
if (fs.existsSync(bcChangesFile)) {
    exec('grep "\\[BC\\]" ' + bcChangesFile, function (error, results) {
        if (results.length > 0) {
            fail('Backward incompatible changes detected');
            markdown(`
## Backward incompatible changes introduced by this PR
${results.toString()}
`);
        }
    });
}
