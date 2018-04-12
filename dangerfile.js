'use strict';

const commits = danger.github.commits;

// Fail when there are fixup/squash commits in the PR
for (const commit of commits) {
    const commitMessage = commit.commit.message;
    if (commitMessage.startsWith('fixup!') || commitMessage.startsWith('squash!')) {
        fail('There are some fixup/squash commits that needs to be applied before merge.');
        break;
    }
}
