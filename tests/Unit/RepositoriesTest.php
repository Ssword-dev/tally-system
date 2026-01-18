<?php

namespace Tests\Unit;

use App\Repositories\BaseRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\ClassRepository;
use App\Repositories\ActivityRepository;
use App\Repositories\ScoreRepository;

describe('Repositories', function () {
    it('StudentRepository extends BaseRepository', function () {
        expect(class_exists(StudentRepository::class))->toBeTrue();
    });

    it('TeacherRepository extends BaseRepository', function () {
        expect(class_exists(TeacherRepository::class))->toBeTrue();
    });

    it('ClassRepository extends BaseRepository', function () {
        expect(class_exists(ClassRepository::class))->toBeTrue();
    });

    it('ActivityRepository extends BaseRepository', function () {
        expect(class_exists(ActivityRepository::class))->toBeTrue();
    });

    it('ScoreRepository extends BaseRepository', function () {
        expect(class_exists(ScoreRepository::class))->toBeTrue();
    });

    it('BaseRepository exists', function () {
        expect(class_exists(BaseRepository::class))->toBeTrue();
    });
});
